<?php

namespace App\Console\Commands;

use DateTime;
use DateTimeZone;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Agrega los eventos del embudo de un dia y los manda al BO.
 *
 * Pasos 1 y 2 del embudo de adquisicion. Los pasos 3, 4 y 5 los anota el BO
 * solo, en Api/StripeController@payTrial -- este sitio no los manda.
 *
 * Diseño (en el repo del BO):
 * docs/superpowers/specs/2026-10-26-embudo-adquisicion-por-sitio-design.md
 *
 * POR QUE AGREGA Y MANDA UNA VEZ AL DIA, y no un POST por evento: este
 * comando no corre en el camino del pago. Un POST por evento le agregaria una
 * llamada de red al checkout, y si el BO esta lento el cliente lo siente. Asi,
 * si un dia falla, el log del dia sigue en disco y se puede reenviar con
 * --date.
 *
 * EL CORTE DEL DIA ES EN EUROPE/BUCHAREST, que es la zona del negocio. Pero
 * esta app corre en UTC (config/app.php) y el canal `activity` rota por dia
 * UTC, asi que un dia de Bucarest SIEMPRE cae a caballo de DOS archivos:
 * empieza a las 22:00 (o 21:00 en verano) UTC del dia anterior. Contar
 * `activity-<fecha>.log` entero daria un dia corrido dos o tres horas, y el
 * paso 4 del embudo dejaria de cerrar con la tarjeta de Acq del dashboard.
 * Por eso se arma la ventana UTC y se filtra por el timestamp de cada linea.
 */
class ReportFunnelDaily extends Command
{
    protected $signature = 'funnel:report
        {--date= : dia de Europe/Bucharest a reportar (Y-m-d). Por defecto ayer}
        {--partial : marcar el dia como incompleto (el dia del deploy)}
        {--dry-run : contar y mostrar, sin mandar nada}';

    protected $description = 'Agrega los eventos del embudo de un dia y los POSTea al BO';

    /**
     * El evento del log de este sitio -> el paso del embudo que el BO espera.
     *
     * `file_uploaded` es el paso 1 de ESTE sitio y no es una visita: es una
     * visita que ya subio un archivo. Esta declarado asi en la definicion de
     * funnel_visits del BO, que avisa que la key no es comparable entre
     * sitios.
     */
    private const PASOS = [
        'file_uploaded' => 'visits',
        'payment_modal_opened' => 'checkout_shown',
    ];

    public function handle(): int
    {
        $fecha = $this->option('date') ?: $this->ayerEnBucarest();

        if (!$this->fechaValida($fecha)) {
            $this->error("--date tiene que ser Y-m-d, vino: {$fecha}");

            return self::FAILURE;
        }

        $pasos = $this->contar($fecha);

        $this->info("funnel {$fecha} (dia de Europe/Bucharest): " . json_encode($pasos));

        if ($pasos === []) {
            // Sin eventos no se manda nada. Mandar ceros seria peor que no
            // mandar: en el embudo del BO un cero es un dato ("nadie llego a
            // este paso") y se veria igual que "no medimos este paso".
            $this->warn('sin eventos en la ventana: no se manda nada');

            return self::SUCCESS;
        }

        if ($this->option('dry-run')) {
            return self::SUCCESS;
        }

        return $this->mandar($fecha, $pasos);
    }

    /**
     * Cuenta los eventos de la ventana UTC que corresponde al dia de
     * Bucarest pedido.
     *
     * @return array<string,int> paso del embudo => cantidad
     */
    private function contar(string $fecha): array
    {
        [$desde, $hasta] = $this->ventanaUtc($fecha);

        $cuentas = [];

        foreach ($this->archivosQueCubren($desde, $hasta) as $archivo) {
            if (!is_readable($archivo)) {
                continue;
            }

            $fp = fopen($archivo, 'r');
            if ($fp === false) {
                continue;
            }

            while (($linea = fgets($fp)) !== false) {
                if (strpos($linea, 'client_event') === false) {
                    continue;
                }

                // Trafico interno (dev/QA) afuera: la ruta /api/log/event ya
                // lo marca con is_internal usando INTERNAL_IPS. Sin esto, dos
                // personas probando el checkout mueven la conversion de un
                // dia flojo.
                if (strpos($linea, '"is_internal":true') !== false) {
                    continue;
                }

                $instante = $this->instanteDeLaLinea($linea);
                if ($instante === null || $instante < $desde || $instante >= $hasta) {
                    continue;
                }

                foreach (self::PASOS as $evento => $paso) {
                    if (strpos($linea, '"event":"' . $evento . '"') !== false) {
                        $cuentas[$paso] = ($cuentas[$paso] ?? 0) + 1;
                        break;
                    }
                }
            }

            fclose($fp);
        }

        return $cuentas;
    }

    private function mandar(string $fecha, array $pasos): int
    {
        $token = (string) config('services.bo.funnel_token');
        $sitio = (int) config('services.bo.website_id');

        if ($token === '') {
            $this->error('services.bo.funnel_token vacio: falta BO_FUNNEL_INGEST_TOKEN en el .env');

            return self::FAILURE;
        }

        if ($sitio === 0) {
            $this->error('services.bo.website_id vacio: falta WEBSITE_ID en el .env');

            return self::FAILURE;
        }

        $url = rtrim((string) config('services.bo.base_uri'), '/') . '/api/funnel/daily';

        try {
            $respuesta = Http::withToken($token)
                ->timeout(20)
                ->acceptJson()
                ->post($url, [
                    'website_id' => $sitio,
                    'date' => $fecha,
                    'partial' => (bool) $this->option('partial'),
                    'steps' => $pasos,
                ]);
        } catch (\Throwable $e) {
            // Falla y lo dice. No hay reintento adentro a proposito: el log
            // del dia sigue en disco (el canal `activity` guarda 30 dias), y
            // `funnel:report --date=` reenvia. Un reintento ciego acá taparia
            // un BO caido durante una semana.
            $this->error('no se pudo postear al BO: ' . $e->getMessage());
            Log::warning('funnel:report fallo', ['date' => $fecha, 'error' => $e->getMessage()]);

            return self::FAILURE;
        }

        if (!$respuesta->successful()) {
            $this->error("el BO respondio {$respuesta->status()}: " . $respuesta->body());

            return self::FAILURE;
        }

        $this->info('enviado: ' . $respuesta->body());

        return self::SUCCESS;
    }

    /**
     * La ventana UTC [desde, hasta) del dia de Bucarest pedido.
     *
     * Los dos extremos se construyen como medianoche LOCAL de Bucarest y se
     * convierten a UTC, asi que los dias de cambio de horario salen bien
     * solos: el ultimo domingo de marzo la ventana dura 23 horas y el de
     * octubre 25. Hacer la cuenta con un offset fijo es el bug que esto evita.
     *
     * @return array{0:DateTime,1:DateTime}
     */
    private function ventanaUtc(string $fecha): array
    {
        $bucarest = new DateTimeZone('Europe/Bucharest');
        $utc = new DateTimeZone('UTC');

        $desde = new DateTime($fecha . ' 00:00:00', $bucarest);
        $hasta = (clone $desde)->modify('+1 day');

        return [$desde->setTimezone($utc), $hasta->setTimezone($utc)];
    }

    /**
     * Los archivos rotados que pueden contener la ventana. Son dos salvo
     * coincidencia, porque la ventana arranca el dia UTC anterior.
     *
     * @return string[]
     */
    private function archivosQueCubren(DateTime $desde, DateTime $hasta): array
    {
        $dias = [$desde->format('Y-m-d')];

        // -1 segundo: `hasta` es exclusivo, y a las 22:00:00 en punto el
        // ultimo instante de la ventana pertenece al dia anterior.
        $ultimo = (clone $hasta)->modify('-1 second')->format('Y-m-d');
        if ($ultimo !== $dias[0]) {
            $dias[] = $ultimo;
        }

        return array_map(
            fn(string $dia) => storage_path("logs/activity-{$dia}.log"),
            $dias
        );
    }

    /** El instante UTC del prefijo `[Y-m-d H:i:s]` de una linea, o null. */
    private function instanteDeLaLinea(string $linea): ?DateTime
    {
        if (!preg_match('/^\[(\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2})/', $linea, $m)) {
            return null;
        }

        // El log se escribe con la zona de la app, que es UTC
        // (config/app.php). Si alguna vez se cambia esa, esto se rompe en
        // silencio -- de ahi que la zona este nombrada aca.
        return new DateTime($m[1], new DateTimeZone('UTC'));
    }

    private function ayerEnBucarest(): string
    {
        return (new DateTime('now', new DateTimeZone('Europe/Bucharest')))
            ->modify('-1 day')
            ->format('Y-m-d');
    }

    private function fechaValida(string $fecha): bool
    {
        $d = DateTime::createFromFormat('Y-m-d', $fecha);

        return $d !== false && $d->format('Y-m-d') === $fecha;
    }
}
