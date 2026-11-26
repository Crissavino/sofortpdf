<?php

namespace App\Src\Funnel;

use Illuminate\Http\Request;

/**
 * De donde salen los campos de atribucion de una fila de `funnel_events`.
 *
 * ========================================================================
 * LOS DECIDE EL SERVIDOR, NO EL CLIENTE
 * ========================================================================
 *
 * Es la misma razon que ya esta escrita al lado de `variant` en
 * `routes/web.php`, y por eso vive en una sola copia: el cliente no manda
 * estos campos, y creerle seria dejar que cualquiera elija si su sesion
 * cuenta como paga.
 *
 * Y no es una precaucion teorica: hasta el 2026-11-17 el beacon leia
 * `has_gclid` y los `utm_*` del body con `$request->input()`, y **los tres
 * beacons del repo mandan solo el nombre del evento**
 * (`tools/show.blade.php:1524`, `payment-modal.blade.php:1355` y `:1139`).
 * O sea que la columna quedaba en 0/NULL siempre. Medido ese dia: 2.082
 * filas en once dias, CERO con gclid y CERO con utm. Los dos consumidores
 * filtran `has_gclid = true` (`ReportFunnelDaily.php:251` y
 * `ExperimentResults.php:137`), asi que `paid_session` --el DENOMINADOR de
 * la metrica norte-- era cero por construccion, y el experimento no tenia
 * brazo medible.
 *
 * El servidor ya lo tiene: `ResolveVad::captureUtmParams()` lo guarda en
 * sesion en la primera visita y no lo sobreescribe despues, asi que la
 * atribucion del aterrizaje sigue disponible en el request del beacon, que
 * es uno posterior.
 *
 * `has_gclid` ES LITERAL: un `gclid` en sesion. Una sesion que vino de ads
 * por `fbclid` o por `utm_*` sola tiene `cameFromAds` en true y NO cuenta
 * acá. Es a proposito: `paid_session` esta definido sobre `has_gclid` en los
 * dos consumidores, y ensancharlo a "todo lo pago" redefiniria la metrica
 * norte. Eso lo decide quien es dueño del KPI, no este archivo. El costo
 * queda declarado: lo pago que no es de Google no entra al denominador.
 */
final class Attribution
{
    /**
     * Las tres unicas claves de `utm_params` que tienen columna.
     *
     * `captureUtmParams()` guarda cinco (`utm_content` y `utm_term` tambien)
     * y le agrega `gclid`; `funnel_events` tiene columna para estas tres.
     */
    public const UTM = ['utm_source', 'utm_medium', 'utm_campaign'];

    /**
     * @return array<string,mixed> las cuatro claves que `EventRow::build()`
     *                             espera, listas para sumar a la entrada
     */
    public static function desdeSesion(Request $request): array
    {
        $gclid = $request->session()->get('gclid');
        $utm   = (array) $request->session()->get('utm_params', []);

        $fila = ['has_gclid' => $gclid !== null && $gclid !== ''];

        foreach (self::UTM as $clave) {
            $valor = $utm[$clave] ?? null;
            // `''` y `null` son lo mismo para esta columna: la fila no trae
            // ese utm. `EventRow::recortarONulo()` ya los unifica, pero
            // dejarlo explicito evita que un `''` viaje como si fuera dato.
            $fila[$clave] = ($valor === null || $valor === '') ? null : (string) $valor;
        }

        return $fila;
    }
}
