<?php

namespace App\Src\Funnel;

use Carbon\Carbon;
use DateTimeInterface;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * Lo unico que escribe en `funnel_events`.
 *
 * ========================================================================
 * NUNCA PUEDE HACER FALLAR UN PEDIDO, Y NUNCA PUEDE FALLAR EN SILENCIO
 * ========================================================================
 *
 * Las dos mitades son igual de importantes y la segunda es la que se olvida.
 * La primera: la escritura se agenda con `app()->terminating()`, o sea corre
 * DESPUES de que la respuesta salio, y cualquier excepcion se captura. Un
 * evento de embudo no vale una pagina caida en un sitio que cobra.
 *
 * La segunda: cada descarte sube un contador del dia y deja una linea de
 * `warning`. Sin eso, "no falla el pedido" significa "no se entera nadie", y
 * eso es exactamente lo que dejo el embudo roto once dias en noviembre de
 * 2026 -- los errores salian por stdout y el cron los mandaba a /dev/null.
 * Un contador que se puede leer es la diferencia entre una tabla vacia porque
 * no vino nadie y una tabla vacia porque se esta descartando todo.
 *
 * `insertOrIgnore` y no `insert`: con el unico sobre `dedupe_key`, un
 * reintento del cliente es un no-op en vez de un error.
 *
 * NO JOINEA NADA. Es la restriccion 2 de la migracion: en el camino del
 * request no se toca `invoices` ni `payments`.
 */
final class EventRecorder
{
    /** Prefijo del contador de descartes, por dia. */
    public const CACHE_DESCARTES = 'funnel:descartes:';

    /**
     * EL CORTE DEL DIA ES EUROPE/BUCHAREST, NO UTC, Y EL QUE MANDA ES EL LECTOR.
     *
     * La app corre en UTC (`config/app.php`), asi que `now()->format('Y-m-d')`
     * da el dia UTC. Pero el unico lector de este contador es
     * `ReportFunnelDaily::informarDescartes()`, y la fecha que le llega es un
     * dia de Europe/Bucharest -- el mismo corte que `ventanaUtc()` usa para los
     * contadores del embudo. Con la etiqueta en UTC, los descartes de la banda
     * 22:00-24:00 UTC (23:00-01:00 o 00:00-02:00 en Bucarest, segun el horario
     * de verano) se le sumaban al dia ANTERIOR y el reporte del dia al que
     * pertenecen los informaba como 0.
     *
     * No es la hora lo que se alinea, es la ETIQUETA: el contador sigue siendo
     * por dia y el TTL sigue contando desde el instante real.
     */
    public const TZ_CORTE = 'Europe/Bucharest';

    /** Con el flag apagado no se escribe nada y no se agenda nada. */
    public static function habilitado(): bool
    {
        return (bool) config('funnel.enabled');
    }

    public static function record(array $fila): void
    {
        if (!self::habilitado()) {
            return;
        }

        app()->terminating(function () use ($fila) {
            self::escribir($fila);
        });
    }

    /**
     * Separada de `record()` para que se pueda ejercitar sin pasar por el
     * ciclo de vida de la app, y para que quede obvio que el try/catch envuelve
     * SOLO la escritura.
     */
    public static function escribir(array $fila): void
    {
        try {
            DB::table('funnel_events')->insertOrIgnore([
                $fila + ['created_at' => now()],
            ]);
        } catch (Throwable $e) {
            self::descartar($fila, $e->getMessage());
        }
    }

    /**
     * La clave del contador de un instante dado, con el corte de Bucarest.
     *
     * Publica a proposito: es lo que un test puede aseverar sin forzar un
     * descarte, y es la forma de que un segundo lector no vuelva a armar la
     * clave a mano con otro criterio.
     */
    public static function claveDelDia(?DateTimeInterface $instante = null): string
    {
        $momento = $instante === null ? now() : Carbon::instance($instante);

        return self::CACHE_DESCARTES
            . $momento->copy()->setTimezone(self::TZ_CORTE)->format('Y-m-d');
    }

    private static function descartar(array $fila, string $motivo): void
    {
        $clave = self::claveDelDia();

        try {
            Cache::add($clave, 0, now()->addDays(30));
            Cache::increment($clave);
        } catch (Throwable $e) {
            // Si ni el contador se puede escribir, queda la linea de log.
        }

        Log::channel('activity')->warning('funnel_event_descartado', [
            'event' => $fila['event'] ?? null,
            'motivo' => mb_substr($motivo, 0, 300),
        ]);
    }
}
