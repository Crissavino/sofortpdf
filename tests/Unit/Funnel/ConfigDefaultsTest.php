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
        // 90, derivado el 2026-11-06 (ver el comentario de config/funnel.php):
        // cubre el techo de 45 dias del backfill del BO y el analisis de un
        // experimento --2 semanas mas los 32 dias de madurez maxima medidos
        // sobre 6.213 rebills-- con el doble de margen, y es la mitad de
        // exposicion que el 180 del placeholder.
        //
        // Lo que este test fija no es tanto el numero como que haya UNO: el
        // barrido y la cookie leen de aca, y dos numeros distintos dejarian
        // una cookie viva sin datos detras.
        $this->assertSame(90, (int) config('funnel.retention_days'));
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
