<?php

namespace App\Console\Commands;

use App\Src\Funnel\ExperimentStats;
use App\Src\Funnel\SampleSize;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
 * El resultado de un experimento del embudo, sobre la metrica norte.
 *
 * NO DECLARA UN GANADOR: informa, y dice por que no se puede declarar cuando
 * no se puede (decision C6/D8 del diseño). Con 3,9 conversiones por dia en
 * este sitio, la salida esperada es "muestra insuficiente" -- y eso es el
 * criterio de salida de la fase B5, no una falla.
 *
 * LA METRICA: first rebills cada 1.000 sesiones pagas, por brazo.
 *
 *   denominador  sesiones DISTINTAS del brazo con `has_gclid` (o sea pagas).
 *   numerador    de esas sesiones, cuantas terminaron en un primer rebill.
 *
 * EL NUMERADOR CRUZA `subscriptions`, y hay que decir por que esta permitido:
 * la restriccion del diseño es no joinear las tablas compartidas EN EL CAMINO
 * DEL REQUEST. Este es un comando de consola que se corre a mano o por cron,
 * asi que no esta en ese camino. Igual se acota por fecha y por los
 * `customer_id` del experimento, que son pocos.
 *
 * LA MADUREZ SE MIDE, NO SE ASUME: un brazo tiene las guardias maduras cuando
 * su ultimo trial tiene mas de 30 dias (el techo de `dispute_rate_30d`, que es
 * la mas lenta de las dos). Medido el 2026-11-06 sobre 6.213 rebills, la
 * cola real de trial -> primer rebill es 32 dias, asi que 30 es el piso
 * razonable y no una constante inventada.
 */
class ExperimentResults extends Command
{
    protected $signature = 'experiment:results
        {--experiment= : nombre del experimento; por defecto el de config/funnel.experiment}
        {--days=30 : ventana de sesiones a mirar}
        {--mde=0.20 : efecto minimo detectable RELATIVO para el tamaño de muestra}
        {--alpha=0.05 : nivel de significancia}
        {--power=0.80 : potencia}';

    protected $description = 'Informa el resultado de un experimento del embudo sobre la metrica norte';

    /** Dias desde el ultimo trial para que las guardias del brazo esten maduras. */
    private const DIAS_MADUREZ_GUARDIAS = 30;

    public function handle(): int
    {
        $experimento = (string) ($this->option('experiment') ?: config('funnel.experiment'));

        if ($experimento === '') {
            $this->warn('no hay experimento activo (funnel.experiment vacio) y no se paso --experiment');

            return self::SUCCESS;
        }

        $dias = max(1, (int) $this->option('days'));
        $desde = now()->subDays($dias)->format('Y-m-d H:i:s');

        $brazos = $this->brazos($experimento, $desde);

        if (count($brazos) < 2) {
            $this->warn("el experimento '{$experimento}' tiene " . count($brazos) . ' brazo(s) con datos: no hay nada que comparar');
            $this->linea($brazos);

            return self::SUCCESS;
        }

        $this->info("experimento '{$experimento}', ultimos {$dias} dias");
        $this->linea($brazos);

        [$a, $b] = array_slice(array_values($brazos), 0, 2);

        // La tasa base para el calculo de muestra sale del brazo de control
        // (el primero). Si es cero no hay base y no se puede calcular: pasa
        // cuando el test arranco recien.
        $base = $a['sesiones'] > 0 ? $a['conversiones'] / $a['sesiones'] : 0.0;

        if ($base <= 0.0) {
            $this->warn('el brazo de control no tiene ninguna conversion todavia: sin tasa base no hay tamaño de muestra');

            return self::SUCCESS;
        }

        $requerida = SampleSize::perArm(
            $base,
            (float) $this->option('mde'),
            (float) $this->option('alpha'),
            (float) $this->option('power')
        );

        $porDia = array_sum(array_column($brazos, 'sesiones')) / $dias;
        $diasFaltan = SampleSize::daysNeeded($requerida, count($brazos), $porDia);

        $this->info(sprintf(
            'muestra requerida: %d sesiones pagas por brazo (base %.2f%%, mde %s%% relativo)%s',
            $requerida,
            $base * 100,
            (float) $this->option('mde') * 100,
            $diasFaltan === null ? '' : " · a {$porDia} sesiones/dia son ~{$diasFaltan} dias de test"
        ));

        $r = ExperimentStats::compare($a, $b, $requerida);

        $this->info(sprintf(
            'diferencia: %+.2f pp (IC95 %+.2f a %+.2f) · %s',
            $r['diff'] * 100,
            $r['ci_low'] * 100,
            $r['ci_high'] * 100,
            $r['significativo'] ? 'el intervalo NO cruza el cero' : 'el intervalo cruza el cero'
        ));

        if ($r['declarable']) {
            $this->info('SE PUEDE DECLARAR: ' . $r['motivo']);
        } else {
            // warn y no error: no es una falla del comando, es el estado del
            // experimento. Un rc distinto de cero haria que un cron lo reporte
            // como roto.
            $this->warn('NO SE PUEDE DECLARAR: ' . $r['motivo']);
        }

        return self::SUCCESS;
    }

    /**
     * Sesiones pagas y primeros rebills por brazo.
     *
     * @return array<string,array{nombre:string,sesiones:int,conversiones:int,guardias_maduras:bool,ultimo_trial:?string}>
     */
    private function brazos(string $experimento, string $desde): array
    {
        $filas = DB::table('funnel_events')
            ->selectRaw('variant, COUNT(DISTINCT session_id) AS sesiones, MAX(occurred_at) AS ultimo')
            ->where('is_internal', false)
            ->where('has_gclid', true)
            ->whereNotNull('variant')
            ->where('variant', '!=', '')
            ->where('occurred_at', '>=', $desde)
            ->groupBy('variant')
            ->orderBy('variant')
            ->get();

        $brazos = [];

        foreach ($filas as $f) {
            $clientes = DB::table('funnel_events')
                ->where('variant', $f->variant)
                ->where('is_internal', false)
                ->where('occurred_at', '>=', $desde)
                ->whereNotNull('customer_id')
                ->distinct()
                ->pluck('customer_id')
                ->all();

            $rebills = 0;
            $ultimoTrial = null;

            if ($clientes !== []) {
                $rebills = DB::table('subscriptions')
                    ->whereIn('customer_id', $clientes)
                    ->whereNotNull('first_subscription_invoice_id')
                    ->count();

                $ultimoTrial = DB::table('subscriptions')
                    ->whereIn('customer_id', $clientes)
                    ->max('trial_started_at');
            }

            $brazos[$f->variant] = [
                'nombre' => (string) $f->variant,
                'sesiones' => (int) $f->sesiones,
                'conversiones' => (int) $rebills,
                // Sin ningun trial todavia, las guardias no pueden estar
                // maduras: no hay cohorte que haya cerrado.
                'guardias_maduras' => $ultimoTrial !== null
                    && strtotime($ultimoTrial) < strtotime('-' . self::DIAS_MADUREZ_GUARDIAS . ' days'),
                'ultimo_trial' => $ultimoTrial,
            ];
        }

        return $brazos;
    }

    private function linea(array $brazos): void
    {
        foreach ($brazos as $b) {
            $norte = $b['sesiones'] > 0 ? $b['conversiones'] / $b['sesiones'] * 1000 : 0.0;

            $this->line(sprintf(
                '  brazo %-4s sesiones pagas %-7d first rebills %-6d norte %7.1f /1000  guardias %s',
                $b['nombre'],
                $b['sesiones'],
                $b['conversiones'],
                $norte,
                $b['guardias_maduras'] ? 'maduras' : 'INMADURAS'
            ));
        }
    }
}
