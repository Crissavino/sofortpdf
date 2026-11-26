<?php

namespace Tests\Unit\Funnel;

use App\Src\Funnel\EventRecorder;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/**
 * La compuerta del flag y el rastro de los descartes. No ejercita el INSERT:
 * `phpunit.xml` tiene la conexion sqlite comentada, asi que un test que
 * escriba pega contra la base real.
 */
class EventRecorderTest extends TestCase
{
    public function test_con_el_flag_apagado_no_escribe_ni_agenda_nada()
    {
        config(['funnel.enabled' => false]);

        $this->assertFalse(EventRecorder::habilitado());

        // Si intentara escribir, esta expectativa lo cazaria.
        DB::shouldReceive('table')->never();

        EventRecorder::record(['event' => 'checkout_view']);
    }

    public function test_un_descarte_deja_rastro_en_vez_de_perderse()
    {
        // La mitad que se olvida: "no falla el pedido" no puede significar
        // "no se entera nadie". Se fuerza el fallo con una fila invalida.
        config(['funnel.enabled' => true]);

        \Illuminate\Support\Facades\Log::shouldReceive('channel')
            ->with('activity')
            ->andReturnSelf();
        \Illuminate\Support\Facades\Log::shouldReceive('warning')
            ->once()
            ->with('funnel_event_descartado', \Mockery::type('array'));

        DB::shouldReceive('table')->andThrow(new \RuntimeException('tabla inexistente'));

        EventRecorder::escribir(['event' => 'checkout_view']);
    }

    /**
     * La etiqueta del contador es el DIA DE BUCAREST, que es el unico corte que
     * su lector (`funnel:report`) conoce. Con la etiqueta en UTC, un descarte de
     * esta banda de dos horas se le sumaba al dia anterior y el reporte del dia
     * al que pertenece informaba 0.
     *
     * Las dos filas de verano son las que hacen que un offset fijo no alcance:
     * en noviembre Bucarest es UTC+2 y en julio UTC+3, asi que la banda que
     * cruza el dia no es la misma.
     *
     * @dataProvider instantesQueCruzanElDia
     */
    public function test_la_clave_del_contador_usa_el_dia_de_bucarest(string $utc, string $diaEsperado)
    {
        $this->assertSame(
            EventRecorder::CACHE_DESCARTES . $diaEsperado,
            EventRecorder::claveDelDia(new \DateTimeImmutable($utc, new \DateTimeZone('UTC')))
        );
    }

    public static function instantesQueCruzanElDia(): array
    {
        return [
            // Invierno, UTC+2: 22:00 UTC ya es el dia siguiente en Bucarest.
            'invierno, dentro del dia' => ['2026-11-05 12:00:00', '2026-11-05'],
            'invierno, borde justo antes' => ['2026-11-05 21:59:59', '2026-11-05'],
            'invierno, cruza el dia' => ['2026-11-05 22:00:00', '2026-11-06'],
            'invierno, fin del dia UTC' => ['2026-11-05 23:30:00', '2026-11-06'],
            // Verano, UTC+3: la banda arranca una hora antes.
            'verano, borde justo antes' => ['2026-07-05 20:59:59', '2026-07-05'],
            'verano, cruza el dia' => ['2026-07-05 21:00:00', '2026-07-06'],
        ];
    }

    public function test_sin_argumento_la_clave_es_la_de_ahora_en_bucarest()
    {
        $esperado = EventRecorder::CACHE_DESCARTES
            . now()->copy()->setTimezone(EventRecorder::TZ_CORTE)->format('Y-m-d');

        $this->assertSame($esperado, EventRecorder::claveDelDia());
    }
}
