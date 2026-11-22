<?php

namespace Tests\Feature\Funnel;

use Tests\TestCase;

/**
 * EL BEACON DE `pay_clicked` TIENE QUE ESTAR ARRIBA DE LAS VALIDACIONES.
 *
 * El paso existe para separar "apreto y no paso nada" de "no apreto"
 * (darryl-253: 162 clicks contra 140.578 visitas, y de un sitio de cinco). El
 * handler del submit arranca con tres validaciones que hacen `return`: termino
 * y condiciones sin tildar, nombre vacio, mail sin arroba. ESOS SON el caso
 * "apreto y no paso nada".
 *
 * O sea que un beacon puesto debajo de ellas --al lado de
 * `payment_attempted`, que es donde parece natural ponerlo-- no registraria
 * ninguno de los tres casos, y el paso mediria lo mismo que
 * `payment_attempted` ya mide. El test no da rojo por un bug de sintaxis: da
 * rojo por un cambio que vuelve inutil al paso, y eso no se ve leyendo el
 * diff.
 *
 * Se mide sobre el fuente, por la misma razon que
 * `ConversionTagsStillFireTest`: renderizar el modal pide sesion y estado de
 * paywall, andamiaje que este repo no tiene.
 */
class PayClickedBeaconTest extends TestCase
{
    private const VISTA = 'views/partials/payment-modal.blade.php';

    public function test_el_beacon_de_pay_clicked_va_antes_de_la_primera_validacion()
    {
        $fuente = file_get_contents(resource_path(self::VISTA));
        $this->assertNotFalse($fuente, 'no se pudo leer el modal de pago');

        $beacon = strpos($fuente, "event: 'pay_clicked'");
        $this->assertNotFalse($beacon, 'el modal de pago ya no emite pay_clicked');

        // La primera de las tres validaciones del handler del submit.
        $primeraValidacion = strpos($fuente, '__m.tcRequired');
        $this->assertNotFalse($primeraValidacion, 'cambio el handler del submit: revisar este test');

        $this->assertLessThan(
            $primeraValidacion,
            $beacon,
            'pay_clicked quedo debajo de una validacion que hace return: deja de contar el click que no llega a pagar'
        );
    }

    public function test_el_click_se_cuenta_una_sola_vez_por_submit_y_no_va_al_datalayer()
    {
        $fuente = file_get_contents(resource_path(self::VISTA));

        // UNA sola emision: dos beacons del mismo evento en el mismo camino
        // duplicarian el paso, y el `dedupe_key` solo los junta si caen en el
        // mismo segundo.
        $this->assertSame(
            1,
            substr_count($fuente, "event: 'pay_clicked'"),
            'pay_clicked se emite mas de una vez en el modal'
        );

        // Y NO al dataLayer: el conjunto de eventos del dataLayer es la señal
        // que recibe Google Ads, y este paso es del embudo propio.
        $this->assertStringNotContainsString(
            "dataLayer.push({ event: 'pay_clicked'",
            $fuente,
            'pay_clicked entro al dataLayer: eso cambia la señal de conversion de Ads'
        );
    }
}
