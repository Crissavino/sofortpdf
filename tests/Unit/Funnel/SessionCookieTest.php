<?php

namespace Tests\Unit\Funnel;

use App\Src\Funnel\SessionCookie;
use PHPUnit\Framework\TestCase;

/**
 * El id de la sesion anonima del embudo.
 *
 * LO QUE ESTOS TESTS FIJAN no es el formato, es que NINGUNA entrada haga
 * fallar el pedido. La cookie la manda el cliente, asi que su valor es
 * hostil por definicion: puede no venir, venir vacia, venir con 10 KB de
 * basura o con una ruta. Si cualquiera de esos casos tira, se cae la pagina
 * de un sitio que cobra -- y el evento del embudo vale infinitamente menos
 * que el pedido.
 *
 * Sin DB y sin Laravel: es una funcion pura.
 */
class SessionCookieTest extends TestCase
{
    public function test_un_valor_valido_se_conserva()
    {
        // Si no se conservara, cada pedido seria una sesion nueva y el
        // denominador de la metrica norte contaria pageviews en vez de
        // sesiones.
        $id = str_repeat('a', 32);

        $this->assertSame($id, SessionCookie::normalize($id));
    }

    public function test_toda_entrada_invalida_acuna_un_id_nuevo_en_vez_de_tirar()
    {
        $malos = [
            'nada' => null,
            'vacia' => '',
            'corta' => 'x',
            'larga' => str_repeat('b', 10000),
            'ruta' => '../../etc/passwd',
            'mayusculas' => str_repeat('A', 32),
            'no-hexa' => str_repeat('z', 32),
            'casi' => str_repeat('a', 31),
            'inyeccion' => "' OR 1=1 --",
            'nulo-adentro' => "aaaa\0aaaa",
        ];

        foreach ($malos as $caso => $valor) {
            $nuevo = SessionCookie::normalize($valor);
            $this->assertMatchesRegularExpression('/^[0-9a-f]{32}$/', $nuevo, $caso);
        }
    }

    public function test_dos_acunaciones_no_coinciden()
    {
        // Un id predecible agruparia visitantes distintos en una sola sesion.
        $this->assertNotSame(
            SessionCookie::normalize(null),
            SessionCookie::normalize(null)
        );
    }

    public function test_el_id_acunado_es_valido_para_la_proxima_vuelta()
    {
        // La propiedad que ata los dos tests de arriba: lo que acuña tiene
        // que pasar su propia validacion, o el visitante estrenaria sesion en
        // cada pedido para siempre.
        $nuevo = SessionCookie::normalize(null);

        $this->assertSame($nuevo, SessionCookie::normalize($nuevo));
    }

    public function test_reconoce_si_un_valor_hay_que_reemplazarlo()
    {
        // Lo usa el middleware para decidir si manda un Set-Cookie: reescribir
        // la cookie en cada pedido es trafico y un Set-Cookie de mas en cada
        // respuesta.
        $this->assertTrue(SessionCookie::esValido(str_repeat('a', 32)));
        $this->assertFalse(SessionCookie::esValido(null));
        $this->assertFalse(SessionCookie::esValido('x'));
    }
}
