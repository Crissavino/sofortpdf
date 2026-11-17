<?php

namespace Tests\Feature\Funnel;

use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/**
 * La fila que escribe el beacon toma la atribucion de la SESION y no del body.
 *
 * Es el test que no existia y por eso el denominador de la metrica norte fue
 * cero once dias: los tres beacons del repo mandan solo el nombre del evento,
 * el route leia `has_gclid` y los `utm_*` con `$request->input()`, y nadie
 * medía de donde salia el valor. Un test de `EventRow::build()` no lo caza:
 * `build()` normaliza bien lo que le dan, y el bug estaba en el LLAMADOR.
 *
 * No toca la base: se intercepta `DB::table()` con el mismo idioma que
 * `EventRecorderTest`, porque `phpunit.xml` tiene la conexion sqlite
 * comentada y un test que escriba pega contra la base real.
 */
class AtribucionDelServidorTest extends TestCase
{
    /** @var array<int,array<string,mixed>> cada fila que el route mando al INSERT */
    private array $filas = [];

    /**
     * La captura se arma UNA vez por test y acumula, para que un caso pueda
     * postear dos veces: el mock de la facade resuelve `table()` con la
     * primera expectativa, asi que una captura por llamada se pierde en la
     * segunda -- y se pierde en silencio, dejando el aserto midiendo un
     * array vacio en vez del camino que cree medir.
     */
    private function capturarFilas(): void
    {
        config(['funnel.enabled' => true]);

        $this->filas = [];
        $tabla = \Mockery::mock();
        $tabla->shouldReceive('insertOrIgnore')
              ->atLeast()->once()
              ->andReturnUsing(function (array $filas) {
                  $this->filas[] = $filas[0];
                  return 1;
              });
        DB::shouldReceive('table')->with('funnel_events')->andReturn($tabla);
    }

    /**
     * @return array<string,mixed> la fila que el route escribio en este post
     *
     * TRAMPA DEL FIXTURE, y no un bug de produccion: `EventRecorder::record()`
     * agenda la escritura con `app()->terminating()`, y `Application::terminate()`
     * de Laravel 8 **no vacia** `terminatingCallbacks`. Dos posts en un mismo
     * test comparten la instancia de la app, asi que el segundo `terminate()`
     * vuelve a correr el callback del primero y la fila del primer post se
     * escribe DOS veces. En produccion cada request es su propia instancia.
     * De ahi que el aserto sea "crecio" y la fila buscada la ULTIMA: pedir
     * `$filas[$antes]` devuelve la fila del post ANTERIOR, o sea un caso que
     * pasa o falla midiendo el request equivocado.
     */
    private function filaEscritaPor(array $sesion, array $body): array
    {
        $antes = count($this->filas);

        $this->withSession($sesion)
             ->postJson('/api/log/event', $body + ['event' => 'payment_modal_opened'])
             ->assertOk();

        $this->assertGreaterThan($antes, count($this->filas), 'el route no escribio una fila nueva');

        return $this->filas[count($this->filas) - 1];
    }

    public function test_el_gclid_de_la_sesion_marca_la_fila_como_paga()
    {
        $this->capturarFilas();

        $fila = $this->filaEscritaPor(['gclid' => 'EAIaIQ-test'], []);

        $this->assertTrue($fila['has_gclid']);
    }

    public function test_sin_gclid_en_sesion_la_fila_no_es_paga()
    {
        $this->capturarFilas();

        $fila = $this->filaEscritaPor([], []);

        $this->assertFalse($fila['has_gclid']);
    }

    public function test_los_utm_salen_de_la_sesion()
    {
        $this->capturarFilas();

        $fila = $this->filaEscritaPor([
            'utm_params' => [
                'utm_source'   => 'google',
                'utm_medium'   => 'cpc',
                'utm_campaign' => 'hu-pdf-to-word',
                // Las dos que no tienen columna, y el gclid que
                // `captureUtmParams()` mete en el mismo array: se ignoran.
                'utm_content'  => 'ad-3',
                'utm_term'     => 'pdf word',
                'gclid'        => 'EAIaIQ-test',
            ],
        ], []);

        $this->assertSame('google', $fila['utm_source']);
        $this->assertSame('cpc', $fila['utm_medium']);
        $this->assertSame('hu-pdf-to-word', $fila['utm_campaign']);
    }

    public function test_sin_utm_en_sesion_las_tres_columnas_quedan_nulas()
    {
        $this->capturarFilas();

        $fila = $this->filaEscritaPor([], []);

        $this->assertNull($fila['utm_source']);
        $this->assertNull($fila['utm_medium']);
        $this->assertNull($fila['utm_campaign']);
    }

    /**
     * EL CASO QUE IMPORTA. Las dos mitades van juntas a proposito: un test
     * que solo niegue ("el body no se cree") se cumple igual con un route
     * que no escriba nada, y uno que solo afirme no prueba que el body dejo
     * de pesar. Aca el body MIENTE en las dos direcciones y pierde.
     */
    public function test_el_body_no_decide_la_atribucion_en_ninguna_direccion()
    {
        $this->capturarFilas();

        // 1. El body se declara pago y la sesion no lo es: no cuenta.
        $inventada = $this->filaEscritaPor([], [
            'has_gclid'  => true,
            'utm_source' => 'inventado',
            'utm_medium' => 'inventado',
            'utm_campaign' => 'inventado',
        ]);

        $this->assertFalse($inventada['has_gclid'], 'el body se hizo pasar por pago');
        $this->assertNull($inventada['utm_source'], 'el utm_source salio del body');
        $this->assertNull($inventada['utm_medium'], 'el utm_medium salio del body');
        $this->assertNull($inventada['utm_campaign'], 'el utm_campaign salio del body');

        // 2. Y al revés: el body se declara no-pago sobre una sesion paga.
        //    Sin esta mitad, un route que escriba `false` fijo pasaria la 1.
        $real = $this->filaEscritaPor(
            ['gclid' => 'EAIaIQ-test', 'utm_params' => ['utm_source' => 'google']],
            ['has_gclid' => false, 'utm_source' => null]
        );

        $this->assertTrue($real['has_gclid'], 'el body apago una sesion paga');
        $this->assertSame('google', $real['utm_source']);
    }
}
