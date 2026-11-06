<?php

namespace Tests\Unit\Funnel;

use Tests\TestCase;

/**
 * Los defaults de config/funnel.php.
 *
 * El unico que importa de verdad es `enabled`: si algun dia alguien le cambia
 * el default a true, el deploy siguiente empieza a setear cookies y a
 * escribir filas en la tabla compartida sin que nadie lo haya decidido. El
 * plan entero se apoya en que esto sale apagado y se prende aparte.
 */
class ConfigDefaultsTest extends TestCase
{
    public function test_arranca_apagado()
    {
        $this->assertFalse(
            (bool) config('funnel.enabled'),
            'funnel.enabled tiene que salir en false: el flip es un paso aparte, despues del smoke'
        );
    }

    public function test_la_retencion_y_la_vida_de_la_cookie_salen_del_mismo_valor()
    {
        // 180 es el [REVISAR] del Anexo B. Lo que este test fija no es el
        // numero sino que haya UNO: el barrido y la cookie leen de aca, y dos
        // numeros distintos dejarian una cookie viva sin datos detras.
        $this->assertSame(180, (int) config('funnel.retention_days'));
    }

    public function test_el_nombre_de_la_cookie_esta_declarado()
    {
        $this->assertNotSame('', (string) config('funnel.cookie'));
    }

    public function test_sin_experimento_activo_por_defecto()
    {
        $this->assertSame('', (string) config('funnel.experiment'));
        $this->assertSame(['A', 'B'], config('funnel.arms'));
    }
}
