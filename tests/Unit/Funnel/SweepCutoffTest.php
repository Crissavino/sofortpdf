<?php

namespace Tests\Unit\Funnel;

use App\Console\Commands\SweepFunnelEvents;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

/**
 * El corte del barrido de retencion. Lo unico puro del comando, y lo unico
 * que puede estar mal de una forma caro: un corte mal calculado borra datos
 * que todavia sirven, y eso no se deshace.
 */
class SweepCutoffTest extends TestCase
{
    public function test_el_corte_son_los_dias_de_retencion_hacia_atras()
    {
        $ahora = new DateTimeImmutable('2026-11-06 11:30:00');

        $this->assertSame('2026-05-10 11:30:00', SweepFunnelEvents::corte(180, $ahora));
    }

    public function test_un_dia_de_retencion_deja_el_dia_de_ayer()
    {
        $ahora = new DateTimeImmutable('2026-11-06 00:00:00');

        $this->assertSame('2026-11-05 00:00:00', SweepFunnelEvents::corte(1, $ahora));
    }

    public function test_el_corte_nunca_cae_en_el_futuro()
    {
        // Un corte futuro borraria lo de hoy. La guarda de `handle()` rechaza
        // dias < 1, y este test fija la otra mitad: con cualquier valor valido
        // el corte queda en el pasado.
        $ahora = new DateTimeImmutable('2026-11-06 11:30:00');

        foreach ([1, 7, 30, 180, 3650] as $dias) {
            $this->assertLessThan(
                $ahora->format('Y-m-d H:i:s'),
                SweepFunnelEvents::corte($dias, $ahora),
                "dias=$dias"
            );
        }
    }

    public function test_cruza_un_cambio_de_mes_y_de_anio()
    {
        $this->assertSame(
            '2025-12-31 23:00:00',
            SweepFunnelEvents::corte(1, new DateTimeImmutable('2026-01-01 23:00:00'))
        );
    }
}
