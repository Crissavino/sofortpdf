<?php

namespace Tests\Feature\Funnel;

use Tests\TestCase;

/**
 * Los eventos que el modal de pago emite siguen estando, SEPARADOS POR DONDE
 * VAN: los que empuja al `dataLayer` (la señal de GA4 / Google Ads) y los que
 * manda como beacon a `/api/log/event` (el embudo propio, que no viaja a Ads).
 *
 * ========================================================================
 * POR QUE ESTAN EN DOS LISTAS Y NO EN UNA
 * ========================================================================
 *
 * Hasta el 2026-11-24 habia UNA lista con un comentario que decia "si este
 * conjunto cambia, cambio la señal que recibe Google Ads". Eso dejo de ser
 * cierto el dia que los beacons de `/api/log/event` entraron a la misma
 * lista: un beacon no lo ve Google Ads, lo ve nuestra propia tabla
 * `funnel_events`. O sea que el renglon describia otra cosa que la que medía,
 * y con las dos clases mezcladas pasaban las dos cosas malas:
 *
 *   - agregar un beacon del embudo obligaba a tocar la lista que custodia la
 *     señal de Ads, asi que el guard mas caro del proyecto se editaba por un
 *     cambio que no lo toca -- y una lista que se edita seguido es una lista
 *     que se actualiza sin leerla;
 *   - y al revés: borrar un `dataLayer.push` se podia "compensar" agregando un
 *     beacon, y el conjunto seguia teniendo el mismo tamaño.
 *
 * Ahora cada lista se lee por su propio mecanismo, anclado a la FUENTE:
 *
 *   - `eventosDelDataLayerDe()` machea `dataLayer.push({ event: '...' })`;
 *   - `eventosDeBeaconDe()` machea el `JSON.stringify({ event: '...' })` que
 *     va DENTRO de un `fetch('/api/log/event', ...)`, asi que tambien ancla el
 *     endpoint: si el beacon se mudara de ruta, esta lista queda vacia y el
 *     test da rojo en vez de seguir contandolo como si nada.
 *
 * Y hay un tercer test que exige que las dos listas CUBRAN todos los
 * `event: '...'` del fuente. Sin el, partir en dos pierde el guard: un evento
 * emitido por un tercer mecanismo (o un beacon a otro endpoint) no caeria en
 * ninguna de las dos listas y ninguna aserción se pondria roja -- que es
 * exactamente la forma de aserto que se satisface con la ausencia del
 * mecanismo.
 *
 * ========================================================================
 * EL CONTEO MEDIDO, Y POR QUE NO SON 9 Y 3
 * ========================================================================
 *
 * Medido el 2026-11-24 sobre el fuente de este taller: 8 eventos de dataLayer
 * y 2 de beacon. El ticket `stanley-294` dice 9 y 3 porque describe el estado
 * CON `darryl-253` adentro, y la base de este taller es anterior a esa rama
 * (`pay_clicked` no aparece en ninguna linea del repo). Cuando `darryl-253`
 * entre, `pay_clicked` suma uno a CADA lista -- esta en los dos caminos -- y
 * ahi los conteos pasan a ser 9 y 3. No se agrego por adelantado: una lista
 * con un evento que el fuente todavia no emite da rojo el dia que se escribe.
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
    private const VISTA_DEL_MODAL = 'partials/payment-modal.blade.php';

    /**
     * Los eventos que el modal empuja al `dataLayer`, medidos el 2026-11-24.
     *
     * ESTA es la lista que custodia la señal de Google Ads: si este conjunto
     * cambia, cambio lo que recibe GA4 / Ads. Un beacon del embudo NO entra
     * acá, justamente para que agregar uno no obligue a tocar esta lista.
     */
    private const EVENTOS_DEL_DATALAYER = [
        'payment_failed',
        'payment_form_opened',
        'payment_form_started',
        'payment_skipped_already_entitled',
        'purchase',
        'signup',
        'tc_details_viewed',
        'try_to_pay',
    ];

    /**
     * Los eventos que el modal manda como beacon a `/api/log/event`, medidos
     * el 2026-11-24.
     *
     * Estos NO los ve Google Ads: terminan en nuestra tabla `funnel_events`.
     * Cambiarlos cambia los pasos del embudo propio, no la señal de la
     * subasta, y por eso tienen lista aparte.
     */
    private const EVENTOS_DE_BEACON = [
        'payment_attempted',
        'payment_modal_opened',
    ];

    private function fuenteDe(string $vista): string
    {
        $fuente = file_get_contents(resource_path('views/' . $vista));
        $this->assertNotFalse($fuente, "no se pudo leer $vista");

        return $fuente;
    }

    /** Ordenado y sin repetidos, para que la aserción no dependa del orden del fuente. */
    private function normalizar(array $eventos): array
    {
        $eventos = array_values(array_unique($eventos));
        sort($eventos);

        return $eventos;
    }

    /**
     * Anclado a `dataLayer.push({ ... })`. El `\s*` después de la llave es
     * necesario: el `purchase` abre el objeto y pone `event:` en la linea
     * siguiente, asi que un patrón de una sola linea lo perderia.
     */
    private function eventosDelDataLayerDe(string $vista): array
    {
        preg_match_all(
            "/dataLayer\.push\(\{\s*event:\s*'([a-z_]+)'/s",
            $this->fuenteDe($vista),
            $m
        );

        return $this->normalizar($m[1]);
    }

    /**
     * Anclado al `fetch('/api/log/event', ...)` ENTERO: el endpoint y el
     * cuerpo en el mismo patrón. La cota de 400 caracteres es lo que impide
     * que un `fetch` a esa ruta se quede con el `JSON.stringify` de otro
     * bloque (en este mismo archivo hay un `JSON.stringify({ payment_method_id
     * : ... })` de la llamada a Stripe, que no es un evento).
     */
    private function eventosDeBeaconDe(string $vista): array
    {
        preg_match_all(
            "/fetch\('\/api\/log\/event'.{0,400}?JSON\.stringify\(\{\s*event:\s*'([a-z_]+)'\s*\}\)/s",
            $this->fuenteDe($vista),
            $m
        );

        return $this->normalizar($m[1]);
    }

    /** Todos los `event: '...'` del fuente, sin importar por dónde salen. */
    private function todosLosEventosDe(string $vista): array
    {
        preg_match_all("/event:\s*'([a-z_]+)'/", $this->fuenteDe($vista), $m);

        return $this->normalizar($m[1]);
    }

    public function test_el_modal_sigue_empujando_al_datalayer_exactamente_estos_eventos()
    {
        $this->assertSame(
            self::EVENTOS_DEL_DATALAYER,
            $this->eventosDelDataLayerDe(self::VISTA_DEL_MODAL),
            'cambio el conjunto de eventos del dataLayer: eso cambia la señal de Google Ads'
        );
    }

    public function test_el_modal_sigue_mandando_a_api_log_event_exactamente_estos_beacons()
    {
        $this->assertSame(
            self::EVENTOS_DE_BEACON,
            $this->eventosDeBeaconDe(self::VISTA_DEL_MODAL),
            'cambiaron los beacons de /api/log/event: eso cambia los pasos del embudo propio, no la señal de Ads'
        );
    }

    public function test_las_dos_listas_cubren_todos_los_eventos_del_fuente()
    {
        // Sin este test, partir en dos pierde cobertura: un evento emitido por
        // un mecanismo que no sea `dataLayer.push` ni el beacon a
        // `/api/log/event` no caeria en ninguna lista y nada se pondria rojo.
        $clasificados = $this->normalizar(array_merge(
            $this->eventosDelDataLayerDe(self::VISTA_DEL_MODAL),
            $this->eventosDeBeaconDe(self::VISTA_DEL_MODAL)
        ));

        $this->assertSame(
            $this->todosLosEventosDe(self::VISTA_DEL_MODAL),
            $clasificados,
            'hay un `event:` en el modal que no es un dataLayer.push ni un beacon a /api/log/event: '
            . 'decidí a cuál de las dos listas pertenece, porque hoy no lo custodia ninguna'
        );
    }

    public function test_el_purchase_sigue_llevando_el_customer_id_en_el_transaction_id()
    {
        // `transaction_id: step1Data.customer_id + '-' + Date.now()` es lo que
        // permite atar un purchase de GA4 a su fila de payments POR CLAVE, y
        // es la razon por la que este sitio se eligio como piloto de B3 (D1
        // del diseño). Si se simplifica a un uuid, el criterio de salida de
        // B3 deja de ser verificable por cliente.
        $fuente = $this->fuenteDe(self::VISTA_DEL_MODAL);

        $this->assertStringContainsString('transaction_id', $fuente);
        $this->assertStringContainsString('customer_id', $fuente);
    }

    public function test_cookiebot_sigue_cargando_antes_de_GTM()
    {
        // El orden es lo que hace que el bloqueo por consentimiento funcione.
        // Y es la razon por la que la cookie del embudo la setea el SERVIDOR:
        // si este orden se invirtiera, el razonamiento de AttachFunnelSession
        // dejaria de aplicar y habria que revisarlo.
        $fuente = $this->fuenteDe('layouts/app.blade.php');

        $posCookiebot = strpos($fuente, 'consent.cookiebot.com');
        $posGtm = strpos($fuente, 'googletagmanager.com');

        $this->assertNotFalse($posCookiebot, 'Cookiebot ya no esta');
        $this->assertNotFalse($posGtm, 'GTM ya no esta');
        $this->assertLessThan($posGtm, $posCookiebot, 'Cookiebot tiene que cargar ANTES de GTM');
    }
}
