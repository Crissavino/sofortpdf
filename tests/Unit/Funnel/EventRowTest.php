<?php

namespace Tests\Unit\Funnel;

use App\Src\Funnel\EventRow;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

/**
 * La fila de `funnel_events`, armada. Pura: ni DB ni Laravel.
 *
 * Dos de estos tests son los que sostienen afirmaciones del diseño que de
 * otro modo serian intenciones: que la tabla no guarda PII, y que un
 * reintento del cliente no infla el paso.
 */
class EventRowTest extends TestCase
{
    private function entrada(array $o = []): array
    {
        return array_merge([
            'session_id' => str_repeat('a', 32),
            'event' => 'checkout_view',
            'website_id' => 6,
            'locale' => 'de',
            'device' => 'desktop',
            'variant' => null,
            'utm_source' => 'google',
            'utm_medium' => 'cpc',
            'utm_campaign' => '12345',
            'has_gclid' => true,
            'is_internal' => false,
            'customer_id' => null,
            'occurred_at' => '2026-11-06 11:30:45',
        ], $o);
    }

    public function test_arma_la_fila_con_las_propiedades_de_la_taxonomia()
    {
        $fila = EventRow::build($this->entrada());

        $this->assertSame('checkout_view', $fila['event']);
        $this->assertSame(6, $fila['website_id']);
        $this->assertSame('de', $fila['locale']);
        $this->assertSame('desktop', $fila['device']);
        $this->assertSame('google', $fila['utm_source']);
        $this->assertTrue($fila['has_gclid']);
        $this->assertFalse($fila['is_internal']);
        $this->assertSame('2026-11-06 11:30:45', $fila['occurred_at']);
    }

    public function test_NO_guarda_ip_ni_user_agent()
    {
        // La asercion que vuelve cierto el "sin PII" del diseño. Se le pasan
        // los dos a proposito: aunque quien llame los mande, no pueden
        // aparecer en la fila.
        $fila = EventRow::build($this->entrada([
            'ip' => '203.0.113.7',
            'user_agent' => 'Mozilla/5.0',
        ]));

        foreach (array_keys($fila) as $columna) {
            $this->assertStringNotContainsString('ip', $columna, "columna sospechosa: $columna");
            $this->assertStringNotContainsString('agent', $columna);
        }
        $this->assertNotContains('203.0.113.7', array_values($fila));
        $this->assertNotContains('Mozilla/5.0', array_values($fila));
    }

    public function test_customer_id_solo_en_trial_started_y_payment_succeeded()
    {
        foreach (['trial_started', 'payment_succeeded'] as $evento) {
            $fila = EventRow::build($this->entrada(['event' => $evento, 'customer_id' => 4242]));
            $this->assertSame(4242, $fila['customer_id'], $evento);
        }

        foreach (['checkout_view', 'landing_view', 'payment_attempted', 'payment_failed'] as $evento) {
            $fila = EventRow::build($this->entrada(['event' => $evento, 'customer_id' => 4242]));
            $this->assertNull($fila['customer_id'], "$evento no puede llevar customer_id");
        }
    }

    public function test_un_evento_fuera_de_la_taxonomia_tira()
    {
        $this->expectException(InvalidArgumentException::class);
        EventRow::build($this->entrada(['event' => 'checkout_viewed']));
    }

    public function test_la_taxonomia_es_exactamente_esta()
    {
        // Agregar un evento tiene que ser deliberado: uno que entra sin pasar
        // por aca es un evento que el BO no espera y que nadie va a leer.
        $this->assertSame([
            'landing_view',
            'tool_started',
            'file_uploaded',
            'processing_completed',
            'paywall_view',
            'checkout_view',
            'payment_attempted',
            'payment_succeeded',
            'payment_failed',
            'trial_started',
            'error_shown',
        ], EventRow::EVENTOS);
    }

    public function test_el_dedupe_key_es_igual_para_el_mismo_evento_sesion_y_segundo()
    {
        // Con el unico de la tabla, esto convierte un doble click o un retry
        // del fetch en un no-op en vez de inflar el paso.
        $a = EventRow::build($this->entrada());
        $b = EventRow::build($this->entrada());

        $this->assertSame($a['dedupe_key'], $b['dedupe_key']);
        $this->assertMatchesRegularExpression('/^[0-9a-f]{40}$/', $a['dedupe_key']);
    }

    public function test_el_dedupe_key_cambia_con_el_evento_la_sesion_o_el_segundo()
    {
        $base = EventRow::build($this->entrada())['dedupe_key'];

        $this->assertNotSame($base, EventRow::build($this->entrada(['event' => 'paywall_view']))['dedupe_key']);
        $this->assertNotSame($base, EventRow::build($this->entrada(['session_id' => str_repeat('b', 32)]))['dedupe_key']);
        $this->assertNotSame($base, EventRow::build($this->entrada(['occurred_at' => '2026-11-06 11:30:46']))['dedupe_key']);
    }

    public function test_recorta_los_campos_largos_en_vez_de_hacer_fallar_el_insert()
    {
        // Un utm_campaign de 5 KB en la URL es trivial de mandar. Con MySQL en
        // modo estricto el INSERT tira y, como la escritura esta en el camino
        // del request, eso seria un error por un dato decorativo.
        $fila = EventRow::build($this->entrada([
            'utm_campaign' => str_repeat('x', 5000),
            'utm_source' => str_repeat('y', 300),
            'locale' => 'de-DE-formal',
            'variant' => str_repeat('z', 90),
        ]));

        $this->assertSame(100, strlen($fila['utm_campaign']));
        $this->assertSame(100, strlen($fila['utm_source']));
        $this->assertSame(5, strlen($fila['locale']));
        $this->assertSame(20, strlen($fila['variant']));
    }

    public function test_un_device_desconocido_no_inventa_una_categoria()
    {
        // Las tasas por dispositivo se leen por categoria: una cuarta
        // categoria que aparece sola las parte sin que nadie lo pida.
        $this->assertSame('other', EventRow::build($this->entrada(['device' => 'smart-fridge']))['device']);
        $this->assertSame('mobile', EventRow::build($this->entrada(['device' => 'mobile']))['device']);
    }
}
