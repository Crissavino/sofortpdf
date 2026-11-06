<?php

namespace Tests\Unit\Funnel;

use App\Src\Funnel\ExperimentStats;
use PHPUnit\Framework\TestCase;

/**
 * El resultado de un experimento, y sobre todo CUANDO se puede declarar.
 *
 * Lo que fijan estos tests es la decision C6 del diseño: el comando INFORMA,
 * no declara. `declarable` es la regla del brief de Ryan --no declarar sin el
 * tamaño de muestra calculado ni con menos de dos semanas-- hecha mecanismo,
 * porque una regla que el agente no puede computar no existe.
 */
class ExperimentStatsTest extends TestCase
{
    private function brazo(string $n, int $sesiones, int $conv, bool $maduras = true): array
    {
        return ['nombre' => $n, 'sesiones' => $sesiones, 'conversiones' => $conv, 'guardias_maduras' => $maduras];
    }

    public function test_el_intervalo_contiene_la_diferencia_y_la_rodea()
    {
        $r = ExperimentStats::compare(
            $this->brazo('A', 4000, 400),
            $this->brazo('B', 4000, 480),
            3840
        );

        // 12 % contra 10 % = +2 puntos
        $this->assertEqualsWithDelta(0.02, $r['diff'], 0.0001);
        $this->assertLessThan($r['diff'], $r['ci_low']);
        $this->assertGreaterThan($r['diff'], $r['ci_high']);
    }

    public function test_con_muestra_suficiente_y_guardias_maduras_es_declarable()
    {
        $r = ExperimentStats::compare(
            $this->brazo('A', 4000, 400),
            $this->brazo('B', 4000, 480),
            3840
        );

        $this->assertTrue($r['declarable'], $r['motivo']);
    }

    public function test_un_brazo_vacio_no_es_declarable()
    {
        $r = ExperimentStats::compare(
            $this->brazo('A', 0, 0),
            $this->brazo('B', 4000, 480),
            3840
        );

        $this->assertFalse($r['declarable']);
        $this->assertStringContainsString('A', $r['motivo']);
    }

    public function test_n_por_debajo_del_tamaño_calculado_no_es_declarable()
    {
        // El caso real de este sitio: datos que parecen suficientes y no lo
        // son. Sin esta regla, 50 contra 60 conversiones se leerian como un
        // ganador del 20 %.
        $r = ExperimentStats::compare(
            $this->brazo('A', 500, 50),
            $this->brazo('B', 500, 60),
            3840
        );

        $this->assertFalse($r['declarable']);
        $this->assertStringContainsString('muestra', $r['motivo']);
        $this->assertStringContainsString('3840', $r['motivo']);
    }

    public function test_las_guardias_inmaduras_bloquean_la_declaracion()
    {
        // Las guardias (frr y dispute_rate_30d) maduran DESPUES de que el test
        // cierra: frr ~22 dias desde el trial, el dispute rate 30. Declarar
        // antes es declarar sin saber si el brazo ganador sube las disputas.
        $r = ExperimentStats::compare(
            $this->brazo('A', 4000, 400, true),
            $this->brazo('B', 4000, 480, false),
            3840
        );

        $this->assertFalse($r['declarable']);
        $this->assertStringContainsString('guardias', $r['motivo']);
    }

    public function test_informa_las_tasas_por_brazo_igual_que_cuando_no_es_declarable()
    {
        // No declarable no es "no mostrar nada": el numero se ve, con el
        // motivo al lado. Esconderlo haria que alguien lo calcule a mano.
        $r = ExperimentStats::compare(
            $this->brazo('A', 500, 50),
            $this->brazo('B', 500, 60),
            3840
        );

        $this->assertEqualsWithDelta(0.10, $r['tasa_a'], 0.0001);
        $this->assertEqualsWithDelta(0.12, $r['tasa_b'], 0.0001);
        $this->assertEqualsWithDelta(0.02, $r['diff'], 0.0001);
    }

    public function test_el_intervalo_que_cruza_el_cero_no_es_un_ganador()
    {
        // Muestra de sobra y una diferencia que no se distingue del ruido.
        $r = ExperimentStats::compare(
            $this->brazo('A', 50000, 5000),
            $this->brazo('B', 50000, 5020),
            3840
        );

        $this->assertTrue($r['declarable'], 'hay muestra, se puede leer');
        $this->assertLessThan(0, $r['ci_low']);
        $this->assertGreaterThan(0, $r['ci_high']);
        $this->assertFalse($r['significativo'], 'el intervalo cruza el cero');
    }

    public function test_una_diferencia_real_sale_significativa()
    {
        $r = ExperimentStats::compare(
            $this->brazo('A', 10000, 1000),
            $this->brazo('B', 10000, 1300),
            3840
        );

        $this->assertTrue($r['significativo']);
        $this->assertGreaterThan(0, $r['ci_low']);
    }
}
