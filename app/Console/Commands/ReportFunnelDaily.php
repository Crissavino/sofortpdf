<?php

namespace App\Console\Commands;

use DateTime;
use DateTimeZone;
use Illuminate\Console\Command;
use App\Src\Funnel\EventRecorder;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
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
    /**
     * El equivalente de PASOS para la tabla, que guarda eventos CANONICOS:
     * `payment_modal_opened` del cliente ya se grabo como `checkout_view`
     * (ver EventRow::DEL_CLIENTE). Son dos mapas porque las dos fuentes
     * hablan dos vocabularios, y unificarlos exigiria reescribir el nombre
     * en el blade del modal de pago.
     */
    private const PASOS_TABLA = [
        'file_uploaded' => 'visits',
        'checkout_view' => 'checkout_shown',
    ];

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

        $delArchivo = $this->contar($fecha);

        // CUAL ES LA FUENTE, y por que no se cambia de golpe: con el flag
        // apagado `funnel_events` esta vacia, asi que leer la tabla mandaria
        // CERO en un sitio que venia reportando -- una regresion silenciosa,
        // que es justo la clase de falla que este proyecto vino a arreglar.
        // Mientras el flag este apagado, la fuente sigue siendo el archivo y
        // este comando se comporta igual que antes.
        $usaTabla = (bool) config('funnel.enabled');
        $pasos = $usaTabla ? $this->contarDeLaTabla($fecha) : $delArchivo;

        $this->info("funnel {$fecha} (dia de Europe/Bucharest, fuente "
            . ($usaTabla ? 'tabla' : 'archivo') . '): ' . json_encode($pasos));

        // LAS DOS FUENTES SE INFORMAN SIEMPRE que la tabla este en uso, y es
        // deliberado: el primer dia despues del flip la tabla tiene el dia a
        // medias (arranco cuando se prendio) y el archivo lo tiene completo.
        // Ver la diferencia es la unica forma de saber si hay que reportar ese
        // dia con --partial, y despues de ese dia es el chequeo de que la
        // tabla dice lo mismo que el archivo.
        if ($usaTabla) {
            $this->info('comparacion con el archivo: ' . json_encode($delArchivo));
            $this->informarDescartes($fecha);

            foreach ($delArchivo as $paso => $cuantos) {
                $deLaTabla = $pasos[$paso] ?? 0;
                if ($cuantos > 0 && abs($deLaTabla - $cuantos) / $cuantos > 0.2) {
                    $this->warn("ATENCION: {$paso} difiere mas del 20% entre tabla ({$deLaTabla}) y archivo ({$cuantos})");
                }
            }
        }

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

    /**
     * EL LECTOR DEL CONTADOR DE DESCARTES, y existe por una razon que este
     * sistema ya documento siete veces: un contador que nadie lee no es un
     * mecanismo, es un artefacto. `EventRecorder` sube ese contador cada vez
     * que una escritura falla y se captura -- sin este renglon, "no falla el
     * pedido" volveria a significar "no se entera nadie", que es exactamente
     * lo que dejo el embudo roto once dias en noviembre de 2026.
     *
     * Va acá y no en un comando propio porque este ya corre todos los dias
     * por cron con la salida a un archivo. Un segundo job seria una pieza mas
     * que instalar en la maquina nueva.
     */
    private function informarDescartes(string $fecha): void
    {
        $n = (int) Cache::get(EventRecorder::CACHE_DESCARTES . $fecha, 0);

        if ($n > 0) {
            // warn y no info: si se descarto algo, los contadores de ese dia
            // son una cota inferior y quien los lea tiene que saberlo.
            $this->warn("ATENCION: {$n} evento(s) descartado(s) el {$fecha}: los contadores de ese dia son un piso");

            return;
        }

        $this->info("descartes del {$fecha}: 0");
    }

    /**
     * Lo mismo que `contar()` pero leyendo `funnel_events`.
     *
     * LOS PASOS CUENTAN OCURRENCIAS, NO SESIONES DISTINTAS, y eso no es un
     * descuido: el archivo cuenta una linea por evento, asi que contar
     * sesiones distintas bajaria `funnel_visits` y `funnel_checkout_shown` de
     * golpe y partiria la serie historica sin que haya pasado nada en el
     * negocio. Si algun dia se quiere la version por sesion, es una key nueva
     * con su propio nombre, no esta.
     *
     * `paid_session` SI cuenta sesiones distintas, porque es su definicion: el
     * denominador de la metrica norte son sesiones, no pageviews. Por eso no
     * sale del archivo -- el archivo no tiene id de sesion.
     *
     * @return array<string,int>
     */
    private function contarDeLaTabla(string $fecha): array
    {
        // `ventanaUtc()` devuelve DateTime, no timestamps -- el camino del
        // archivo los compara contra el `getTimestamp()` de cada linea. Aca se
        // formatean directo, que es lo que espera la columna `occurred_at`
        // (datetime en UTC, igual que la app).
        [$desde, $hasta] = $this->ventanaUtc($fecha);
        $desdeSql = $desde->format('Y-m-d H:i:s');
        $hastaSql = $hasta->format('Y-m-d H:i:s');

        $base = DB::table('funnel_events')
            ->where('is_internal', false)
            ->where('occurred_at', '>=', $desdeSql)
            ->where('occurred_at', '<', $hastaSql);

        $cuentas = [];

        foreach (self::PASOS_TABLA as $evento => $paso) {
            $n = (clone $base)->where('event', $evento)->count();
            if ($n > 0) {
                $cuentas[$paso] = $n;
            }
        }

        $sesionesPagas = (clone $base)
            ->where('has_gclid', true)
            ->distinct()
            ->count('session_id');

        if ($sesionesPagas > 0) {
            $cuentas['paid_session'] = $sesionesPagas;
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
