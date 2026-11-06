<?php

namespace Tests\Unit\Funnel;

use App\Src\Funnel\VariantAssigner;
use PHPUnit\Framework\TestCase;

/**
 * La asignacion de brazo de experimento. Pura y sin estado.
 *
 * Lo que fijan estos tests son las tres propiedades sin las cuales un A/B no
 * se puede leer: que la misma sesion caiga siempre en el mismo brazo, que el
 * reparto sea parejo, y que dos experimentos no compartan la particion.
 */
class VariantAssignerTest extends TestCase
{
    private const BRAZOS = ['A', 'B'];

    private function sesion(int $n): string
    {
        return md5('sesion-' . $n);
    }

    public function test_la_misma_sesion_siempre_cae_en_el_mismo_brazo()
    {
        // Sin esto el visitante ve la variante A en la landing y la B en el
        // checkout, y el experimento no mide nada.
        $id = $this->sesion(1);
        $primero = VariantAssigner::for($id, 'checkout-copy', self::BRAZOS);

        for ($i = 0; $i < 50; $i++) {
            $this->assertSame($primero, VariantAssigner::for($id, 'checkout-copy', self::BRAZOS));
        }
    }

    public function test_el_reparto_es_parejo_dentro_del_2_por_ciento()
    {
        $n = 100000;
        $conteo = ['A' => 0, 'B' => 0];

        for ($i = 0; $i < $n; $i++) {
            $conteo[VariantAssigner::for($this->sesion($i), 'checkout-copy', self::BRAZOS)]++;
        }

        foreach ($conteo as $brazo => $cuantos) {
            $desvio = abs($cuantos / $n - 0.5);
            $this->assertLessThan(0.02, $desvio, "brazo $brazo: " . round($cuantos / $n * 100, 2) . '%');
        }
    }

    public function test_dos_experimentos_no_comparten_la_particion()
    {
        // Si la particion fuera la misma, el segundo test heredaria el sesgo
        // del primero: los que respondieron mejor a A quedarian otra vez
        // juntos, y la diferencia se leeria como efecto del cambio nuevo.
        $iguales = 0;
        $n = 2000;

        for ($i = 0; $i < $n; $i++) {
            $id = $this->sesion($i);
            if (VariantAssigner::for($id, 'uno', self::BRAZOS) === VariantAssigner::for($id, 'dos', self::BRAZOS)) {
                $iguales++;
            }
        }

        // Con particiones independientes coinciden ~50%: lo que se rechaza es
        // el 100%, que seria la misma particion.
        $this->assertLessThan(0.6, $iguales / $n);
        $this->assertGreaterThan(0.4, $iguales / $n);
    }

    public function test_sin_experimento_activo_no_hay_variante()
    {
        // Cadena vacia y no 'A': una fila con variante cuando no hay
        // experimento haria que el analisis del proximo test arrastre filas
        // de un periodo sin test.
        $this->assertSame('', VariantAssigner::for($this->sesion(1), '', self::BRAZOS));
    }

    public function test_sin_brazos_no_hay_variante()
    {
        $this->assertSame('', VariantAssigner::for($this->sesion(1), 'checkout-copy', []));
    }

    public function test_sin_sesion_no_hay_variante()
    {
        // Una sesion vacia no se puede asignar de forma estable, y meterla en
        // un brazo cualquiera la contaria en el denominador de ese brazo.
        $this->assertSame('', VariantAssigner::for('', 'checkout-copy', self::BRAZOS));
    }

    public function test_funciona_con_mas_de_dos_brazos()
    {
        $brazos = ['A', 'B', 'C'];
        $conteo = ['A' => 0, 'B' => 0, 'C' => 0];

        for ($i = 0; $i < 30000; $i++) {
            $conteo[VariantAssigner::for($this->sesion($i), 'tres', $brazos)]++;
        }

        foreach ($conteo as $brazo => $cuantos) {
            $this->assertLessThan(0.02, abs($cuantos / 30000 - 1 / 3), "brazo $brazo");
        }
    }

    public function test_agregar_un_brazo_reasigna_a_todos_y_eso_es_esperado()
    {
        // Documenta la consecuencia en vez de esconderla: el modulo cambia,
        // asi que cambiar la cantidad de brazos en medio de un test invalida
        // lo corrido. Hay que arrancar un experimento nuevo, con otro nombre.
        $id = $this->sesion(7);

        $this->assertNotSame(
            VariantAssigner::for($id, 'x', ['A', 'B']),
            VariantAssigner::for($id, 'x', ['A', 'B', 'C', 'D'])
        );
    }
}
