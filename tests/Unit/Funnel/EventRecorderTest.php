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
}
