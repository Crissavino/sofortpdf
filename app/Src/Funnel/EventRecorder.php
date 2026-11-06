<?php

namespace App\Src\Funnel;

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

    private static function descartar(array $fila, string $motivo): void
    {
        $clave = self::CACHE_DESCARTES . now()->format('Y-m-d');

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
