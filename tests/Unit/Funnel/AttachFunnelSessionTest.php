<?php

namespace Tests\Unit\Funnel;

use App\Http\Kernel;
use App\Http\Middleware\AttachFunnelSession;
use App\Src\Funnel\SessionCookie;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Tests\TestCase;

/**
 * El middleware que setea la cookie del embudo.
 *
 * El test que importa es el PRIMERO: con el flag apagado, el middleware no
 * puede dejar ninguna huella en la respuesta. De eso depende que el deploy a
 * un sitio que cobra --sin staging-- sea inocuo, y que el unico momento
 * riesgoso sea el flip de la variable de entorno.
 */
class AttachFunnelSessionTest extends TestCase
{
    private function correr(Request $request): Response
    {
        return (new AttachFunnelSession())->handle(
            $request,
            fn() => new Response('ok')
        );
    }

    public function test_con_el_flag_apagado_no_deja_ninguna_huella()
    {
        config(['funnel.enabled' => false]);
        $request = Request::create('/de/pdf-zu-word');

        $respuesta = $this->correr($request);

        $this->assertSame([], $respuesta->headers->getCookies(), 'no puede mandar Set-Cookie');
        $this->assertFalse(
            $request->attributes->has(AttachFunnelSession::ATRIBUTO),
            'no puede ni tocar el request'
        );
    }

    public function test_con_el_flag_prendido_y_sin_cookie_acuna_y_la_manda()
    {
        config(['funnel.enabled' => true, 'funnel.cookie' => 'fe_sid', 'funnel.retention_days' => 180]);
        $request = Request::create('https://sofortpdf.com/de/pdf-zu-word');

        $respuesta = $this->correr($request);

        $id = $request->attributes->get(AttachFunnelSession::ATRIBUTO);
        $this->assertTrue(SessionCookie::esValido($id));

        $cookies = $respuesta->headers->getCookies();
        $this->assertCount(1, $cookies);
        $this->assertSame('fe_sid', $cookies[0]->getName());
        $this->assertSame($id, $cookies[0]->getValue());
        $this->assertTrue($cookies[0]->isHttpOnly(), 'ningun script la necesita');
        $this->assertSame('lax', $cookies[0]->getSameSite());
        $this->assertTrue($cookies[0]->isSecure(), 'sobre https tiene que ir Secure');
    }

    public function test_con_una_cookie_valida_la_respeta_y_NO_vuelve_a_mandarla()
    {
        // Un Set-Cookie en cada respuesta del sitio es un header de mas por
        // pedido que no cambia nada: el valor ya es el correcto.
        config(['funnel.enabled' => true, 'funnel.cookie' => 'fe_sid', 'funnel.retention_days' => 180]);
        $id = str_repeat('c', 32);
        $request = Request::create('/de/pdf-zu-word', 'GET', [], ['fe_sid' => $id]);

        $respuesta = $this->correr($request);

        $this->assertSame($id, $request->attributes->get(AttachFunnelSession::ATRIBUTO));
        $this->assertSame([], $respuesta->headers->getCookies());
    }

    public function test_con_una_cookie_corrupta_acuna_una_nueva_sin_tirar()
    {
        config(['funnel.enabled' => true, 'funnel.cookie' => 'fe_sid', 'funnel.retention_days' => 180]);
        $request = Request::create('/de/pdf-zu-word', 'GET', [], ['fe_sid' => '../../etc/passwd']);

        $respuesta = $this->correr($request);

        $this->assertTrue(SessionCookie::esValido($request->attributes->get(AttachFunnelSession::ATRIBUTO)));
        $this->assertCount(1, $respuesta->headers->getCookies());
    }

    public function test_esta_cableado_en_el_grupo_web()
    {
        // El cableado se testea aparte de lo cableado: un middleware
        // impecable que nadie registra no setea una sola cookie, y nada lo
        // dice.
        $kernel = new \ReflectionClass(Kernel::class);
        $grupos = $kernel->getDefaultProperties()['middlewareGroups'] ?? [];

        $this->assertContains(AttachFunnelSession::class, $grupos['web'] ?? []);
    }
}
