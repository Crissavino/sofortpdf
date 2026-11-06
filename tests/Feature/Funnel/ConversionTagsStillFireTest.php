<?php

namespace Tests\Feature\Funnel;

use Tests\TestCase;

/**
 * Los eventos de GA4 / Ads que las vistas empujan al dataLayer siguen estando.
 *
 * ========================================================================
 * POR QUE ESTE TEST ARRANCA EN VERDE, Y ESO ESTA BIEN
 * ========================================================================
 *
 * Es el unico test de este plan que no sale de un rojo. Lo que prueba no es
 * que algo nuevo funcione: es que algo viejo NO SE ROMPIO. El anti-goal mas
 * caro del diseño es "que un tag de conversion de Ads deje de disparar" --
 * si eso pasa, el proyecto es perdida neta sin importar lo demas, porque
 * Smart Bidding se queda sin senal y la cuenta entra en reaprendizaje sin que
 * nadie lo haya decidido.
 *
 * La forma correcta de usarlo es verlo verde ANTES de tocar nada, y verlo
 * verde otra vez con el flag del embudo prendido.
 *
 * ========================================================================
 * ES SOBRE EL FUENTE DE LAS VISTAS, NO SOBRE EL HTML RENDERIZADO
 * ========================================================================
 *
 * Y hay que decirlo porque es una limitacion real: este test NO prueba que el
 * push se ejecute en el navegador. Las paginas de herramienta redirigen o
 * necesitan sesion y estado de paywall, asi que renderizarlas en un test
 * pediria un andamiaje que hoy este repo no tiene (tiene tres tests de
 * Feature, dos de ellos de API).
 *
 * Lo que SI caza, que es el riesgo real de este proyecto: que un refactor o
 * un cambio de nombre se lleve un push, o que alguien lo borre creyendo que
 * sobra. Eso es un cambio en el fuente y acá se ve.
 */
class ConversionTagsStillFireTest extends TestCase
{
    /**
     * Los eventos del modal de pago, medidos el 2026-11-06 ANTES de tocar
     * nada. Si este conjunto cambia, cambio la señal que recibe Google Ads.
     */
    private const EVENTOS_DEL_MODAL = [
        'payment_attempted',
        'payment_failed',
        'payment_form_opened',
        'payment_form_started',
        'payment_modal_opened',
        'payment_skipped_already_entitled',
        'purchase',
        'signup',
        'tc_details_viewed',
        'try_to_pay',
    ];

    private function eventosDe(string $vista): array
    {
        $fuente = file_get_contents(resource_path('views/' . $vista));
        $this->assertNotFalse($fuente, "no se pudo leer $vista");

        preg_match_all("/event:\s*'([a-z_]+)'/", $fuente, $m);
        $eventos = array_values(array_unique($m[1]));
        sort($eventos);

        return $eventos;
    }

    public function test_el_modal_de_pago_sigue_empujando_exactamente_estos_eventos()
    {
        $this->assertSame(
            self::EVENTOS_DEL_MODAL,
            $this->eventosDe('partials/payment-modal.blade.php'),
            'cambio el conjunto de eventos del dataLayer: eso cambia la señal de Google Ads'
        );
    }

    public function test_el_purchase_sigue_llevando_el_customer_id_en_el_transaction_id()
    {
        // `transaction_id: step1Data.customer_id + '-' + Date.now()` es lo que
        // permite atar un purchase de GA4 a su fila de payments POR CLAVE, y
        // es la razon por la que este sitio se eligio como piloto de B3 (D1
        // del diseño). Si se simplifica a un uuid, el criterio de salida de
        // B3 deja de ser verificable por cliente.
        $fuente = file_get_contents(resource_path('views/partials/payment-modal.blade.php'));

        $this->assertStringContainsString('transaction_id', $fuente);
        $this->assertStringContainsString('customer_id', $fuente);
    }

    public function test_cookiebot_sigue_cargando_antes_de_GTM()
    {
        // El orden es lo que hace que el bloqueo por consentimiento funcione.
        // Y es la razon por la que la cookie del embudo la setea el SERVIDOR:
        // si este orden se invirtiera, el razonamiento de AttachFunnelSession
        // dejaria de aplicar y habria que revisarlo.
        $fuente = file_get_contents(resource_path('views/layouts/app.blade.php'));

        $posCookiebot = strpos($fuente, 'consent.cookiebot.com');
        $posGtm = strpos($fuente, 'googletagmanager.com');

        $this->assertNotFalse($posCookiebot, 'Cookiebot ya no esta');
        $this->assertNotFalse($posGtm, 'GTM ya no esta');
        $this->assertLessThan($posGtm, $posCookiebot, 'Cookiebot tiene que cargar ANTES de GTM');
    }
}
