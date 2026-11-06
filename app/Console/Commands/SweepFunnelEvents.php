<?php

namespace App\Console\Commands;

use DateTimeImmutable;
use DateTimeInterface;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Borra las filas de `funnel_events` mas viejas que la ventana de retencion.
 *
 * POR QUE EXISTE: la tabla guarda `customer_id` en `trial_started` y
 * `payment_succeeded`, asi que es PSEUDONIMA y no anonima -- entra al regimen
 * de borrado y de pedidos de acceso. Una retencion declarada que nada aplica
 * es una promesa incumplida, y es la clase de artefacto que este sistema ya
 * documento siete veces: lo que nadie corre, no existe.
 *
 * BORRA EN TANDAS, y esa es la unica decision importante del comando. La
 * tabla vive en `avocode_production`, el esquema COMPARTIDO: el 2026-11-03 un
 * SELECT largo sobre `invoices` de esa misma base la trabo 85 minutos y tiro
 * el checkout de cuatro sitios. Un `DELETE` de 180 dias de una tabla con
 * millones de filas es exactamente la misma forma de romper algo. Con tandas
 * acotadas, cada transaccion es corta y el resto del esquema no lo siente.
 *
 * CORRE CON EL FLAG APAGADO TAMBIEN, a proposito: si se escribio durante una
 * semana y despues se apago el flag, esas filas igual tienen que expirar.
 *
 * Diseño (repo del BO):
 * docs/superpowers/specs/2026-11-05-eventos-por-sesion-y-experimentos-design.md
 */
class SweepFunnelEvents extends Command
{
    protected $signature = 'funnel:sweep
        {--dry-run : contar lo que borraria, sin borrar}
        {--batch=1000 : filas por tanda}';

    protected $description = 'Borra los eventos del embudo mas viejos que funnel.retention_days';

    /**
     * Techo de tandas por corrida. Con el default son 500.000 filas, mucho mas
     * de lo que un dia genera. Existe para que un error de calculo del corte
     * no deje al comando borrando la tabla entera en un solo turno: es mejor
     * que quede trabajo para mañana que una corrida de horas sobre la base
     * compartida.
     */
    private const MAX_TANDAS = 500;

    public function handle(): int
    {
        $dias = (int) config('funnel.retention_days');

        if ($dias < 1) {
            // Un cero o un negativo borraria TODO, incluido lo de hoy. Si el
            // valor de config esta mal, no se adivina: se corta.
            $this->error("funnel.retention_days tiene que ser >= 1, vino: {$dias}");

            return self::FAILURE;
        }

        $corte = self::corte($dias, new DateTimeImmutable('now'));
        $tanda = max(1, (int) $this->option('batch'));

        $viejas = DB::table('funnel_events')->where('occurred_at', '<', $corte)->count();

        $this->info("corte: {$corte} ({$dias} dias) · filas mas viejas: {$viejas}");

        if ($this->option('dry-run')) {
            return self::SUCCESS;
        }

        $borradas = 0;
        for ($i = 0; $i < self::MAX_TANDAS; $i++) {
            $n = DB::table('funnel_events')
                ->where('occurred_at', '<', $corte)
                ->limit($tanda)
                ->delete();

            $borradas += $n;

            if ($n < $tanda) {
                break;
            }
        }

        $this->info("borradas: {$borradas}");

        if ($borradas > 0) {
            // Al log, no solo a stdout: el cron manda la salida a un archivo,
            // pero una linea en el log de la app es lo que se puede cruzar con
            // el resto. La leccion del 2026-11-05 es que stdout solo no
            // alcanza.
            Log::channel('activity')->info('funnel:sweep', [
                'corte' => $corte,
                'borradas' => $borradas,
            ]);
        }

        if ($borradas >= self::MAX_TANDAS * $tanda) {
            $this->warn('se alcanzo el techo de tandas: queda trabajo para la proxima corrida');
        }

        return self::SUCCESS;
    }

    /**
     * El instante antes del cual una fila esta vencida. Pura para que se pueda
     * testear sin DB ni Laravel.
     */
    public static function corte(int $dias, DateTimeInterface $ahora): string
    {
        return (new DateTimeImmutable('@' . $ahora->getTimestamp()))
            ->modify("-{$dias} days")
            ->format('Y-m-d H:i:s');
    }
}
