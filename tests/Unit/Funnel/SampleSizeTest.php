<?php

namespace Tests\Unit\Funnel;

use App\Src\Funnel\SampleSize;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

/**
 * La calculadora de tamaño de muestra.
 *
 * ES LA PIEZA MAS IMPORTANTE DE B5, y no por la formula: es lo que contesta
 * "¿se puede testear aca?" ANTES de correr un test, en vez de descubrirlo
 * despues de tres semanas. Con 3,9 conversiones por dia en este sitio, la
 * respuesta esperada es que NO -- y que la calculadora lo diga es el criterio
 * de salida de la fase, no una falla.
 */
class SampleSizeTest extends TestCase
{
    public function test_contra_un_valor_conocido_de_tabla()
    {
        // Base 10 %, efecto minimo detectable 20 % RELATIVO (o sea 10 % -> 12 %),
        // alpha 0,05 a dos colas, potencia 0,8. El valor de tabla es ~3.840
        // por brazo.
        $n = SampleSize::perArm(0.10, 0.20, 0.05, 0.80);

        $this->assertGreaterThan(3800, $n);
        $this->assertLessThan(3900, $n);
    }

    public function test_un_efecto_mas_chico_pide_mas_muestra()
    {
        $grande = SampleSize::perArm(0.10, 0.40, 0.05, 0.80);
        $chico = SampleSize::perArm(0.10, 0.10, 0.05, 0.80);

        $this->assertGreaterThan($grande, $chico);
        // Y la relacion es cuadratica: la mitad del efecto, cuatro veces la
        // muestra. Es lo que hace que un test "un poquito mas fino" sea
        // carisimo, y conviene que el numero lo muestre.
        $this->assertGreaterThan($grande * 3, $chico);
    }

    public function test_mas_potencia_pide_mas_muestra()
    {
        $this->assertGreaterThan(
            SampleSize::perArm(0.10, 0.20, 0.05, 0.80),
            SampleSize::perArm(0.10, 0.20, 0.05, 0.95)
        );
    }

    public function test_los_cuantiles_normales_son_los_de_tabla()
    {
        // La aproximacion que usa adentro: si se degrada, todo lo demas da
        // numeros plausibles y equivocados.
        $this->assertEqualsWithDelta(1.959964, SampleSize::z(0.975), 0.0005);
        $this->assertEqualsWithDelta(0.841621, SampleSize::z(0.80), 0.0005);
        $this->assertEqualsWithDelta(1.281552, SampleSize::z(0.90), 0.0005);
        $this->assertEqualsWithDelta(2.326348, SampleSize::z(0.99), 0.0005);
    }

    public function test_una_tasa_base_imposible_tira()
    {
        foreach ([0.0, 1.0, -0.1, 1.5] as $mala) {
            try {
                SampleSize::perArm($mala, 0.20, 0.05, 0.80);
                $this->fail("deberia tirar con baseRate=$mala");
            } catch (InvalidArgumentException $e) {
                $this->assertStringContainsString('baseRate', $e->getMessage());
            }
        }
    }

    public function test_un_efecto_cero_o_negativo_tira()
    {
        // Sin efecto minimo declarado, la muestra es infinita. Devolver un
        // numero enorme seria peor que tirar: alguien lo tomaria por una
        // respuesta.
        foreach ([0.0, -0.1] as $malo) {
            try {
                SampleSize::perArm(0.10, $malo, 0.05, 0.80);
                $this->fail("deberia tirar con mde=$malo");
            } catch (InvalidArgumentException $e) {
                $this->assertStringContainsString('mde', $e->getMessage());
            }
        }
    }

    public function test_el_efecto_no_puede_pasar_la_tasa_del_100_por_ciento()
    {
        $this->expectException(InvalidArgumentException::class);
        SampleSize::perArm(0.80, 0.50, 0.05, 0.80);
    }

    public function test_estima_los_dias_que_hacen_falta()
    {
        // La traduccion que de verdad se lee: no "3.840 por brazo" sino
        // "cuanto tiempo". Con 109 sesiones pagas por dia repartidas en dos
        // brazos, 3.840 por brazo son ~70 dias.
        $dias = SampleSize::daysNeeded(3840, 2, 109.0);

        $this->assertSame(71, $dias);
    }

    public function test_sin_trafico_no_estima_dias()
    {
        $this->assertNull(SampleSize::daysNeeded(3840, 2, 0.0));
    }
}
