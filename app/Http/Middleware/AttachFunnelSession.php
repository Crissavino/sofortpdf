<?php

namespace App\Http\Middleware;

use App\Src\Funnel\SessionCookie;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Cookie;

/**
 * Pone el id de la sesion anonima del embudo en la cookie, y lo deja en el
 * request para que la ruta de eventos lo lea sin volver a decidir nada.
 *
 * ========================================================================
 * LA SETEA EL SERVIDOR, Y ESA ES LA DECISION ENTERA
 * ========================================================================
 *
 * `resources/views/layouts/app.blade.php` carga Cookiebot con
 * `data-blockingmode="auto"` ANTES de GTM, justamente para bloquear cookies
 * hasta el consentimiento. Ese bloqueo opera sobre SCRIPTS: una cookie puesta
 * desde GTM o desde un `document.cookie` quedaria bloqueada en todo visitante
 * que no acepta, y el denominador de la metrica norte saldria incompleto SIN
 * QUE NADA AVISE -- que es la peor forma de equivocarse, porque el numero
 * sigue pareciendo valido.
 *
 * Puesta desde el servidor, en la respuesta, no pasa por ese bloqueo. Lo que
 * la habilita es que sea EXENTA, y lo que la hace exenta es lo que NO guarda:
 * un id aleatorio, sin IP, sin user-agent, sin fingerprint, sin terceros (ver
 * SessionCookie). Si algun dia se le agrega cualquiera de esas cosas, esta
 * cookie pasa a necesitar consentimiento y este middleware deja de ser
 * correcto.
 *
 * ========================================================================
 * NO HACE NADA CON EL FLAG APAGADO
 * ========================================================================
 *
 * Con `funnel.enabled` en false no setea cookie, no toca el request y no
 * agrega un solo header. El camino del pedido queda IDENTICO al de antes de
 * que este archivo existiera. Eso es lo que permite deployar esto en un sitio
 * que cobra, sin staging, y dejar el riesgo concentrado en el flip -- que es
 * una variable de entorno y se revierte con otra.
 */
class AttachFunnelSession
{
    /** La clave con la que la ruta de eventos lee el id del request. */
    public const ATRIBUTO = 'funnel_session_id';

    public function handle(Request $request, Closure $next)
    {
        if (!config('funnel.enabled')) {
            return $next($request);
        }

        $nombre = (string) config('funnel.cookie');
        $vino = $request->cookie($nombre);

        // `$vino` puede no ser string: la cookie llega desencriptada por
        // EncryptCookies y un valor corrupto puede volver como array.
        $id = SessionCookie::normalize(is_string($vino) ? $vino : null);

        $request->attributes->set(self::ATRIBUTO, $id);

        $respuesta = $next($request);

        // Solo se manda Set-Cookie cuando hace falta. Reescribirla en cada
        // pedido seria un header de mas en cada respuesta del sitio, y no
        // cambiaria nada: el valor es el mismo.
        // `instanceof` y NO `method_exists($respuesta, 'headers')`: `headers`
        // es una PROPIEDAD, asi que method_exists devuelve false siempre y la
        // cookie no se mandaba nunca. El embudo habria quedado con cero
        // sesiones, que desde el panel se lee como "no vino nadie" en vez de
        // como un bug. Lo cazo test_con_una_cookie_corrupta_acuna_una_nueva.
        $puedeSetear = $respuesta instanceof \Symfony\Component\HttpFoundation\Response;

        if ($puedeSetear && !SessionCookie::esValido(is_string($vino) ? $vino : null)) {
            $respuesta->headers->setCookie(new Cookie(
                $nombre,
                $id,
                // La vida de la cookie es la ventana de retencion: una cookie
                // de medicion que dura mas que los datos que mide no tiene
                // para que existir.
                time() + ((int) config('funnel.retention_days') * 86400),
                '/',
                null,
                $request->isSecure(),
                true,       // httpOnly: ningun script la necesita
                false,
                Cookie::SAMESITE_LAX
            ));
        }

        return $respuesta;
    }
}
