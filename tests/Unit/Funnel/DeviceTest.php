<?php

namespace Tests\Unit\Funnel;

use App\Src\Funnel\Device;
use App\Src\Funnel\EventRow;
use Tests\TestCase;

/**
 * El clasificador de `device`, calibrado contra los User-Agent REALES que
 * pegaron en `POST /api/log/event` en produccion (el `access.log` de nginx,
 * leido el 2026-11-17): 16 UA distintos, 141 requests.
 *
 * La muestra NO TIENE NI UNA TABLET, y por eso el caso de tablet es
 * sintetico y esta marcado como tal: si manana aparece una y se clasifica
 * mal, el aserto que lo cazaria es el de la regla, no el de la muestra.
 */
class DeviceTest extends TestCase
{
    /** @return array<int,array{0:int,1:string}> [requests, user-agent] */
    private function uasDeProduccion(): array
    {
        return [
            [83, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/157.0.0.0 Safari/537.36'],
            [13, 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/157.0.0.0 Mobile Safari/537.36'],
            [11, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:160.0) Gecko/20100101 Firefox/160.0'],
            [5, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/157.0.0.0 Safari/537.36 OPR/140.0.0.0'],
            [3, 'Mozilla/5.0 (iPhone; CPU iPhone OS 27_0_1 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) GSA/446.3.1011462875 Mobile/15E148 Safari/604.1'],
            [3, 'Mozilla/5.0 (iPhone; CPU iPhone OS 18_7 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/26.6.1 Mobile/15E148 Safari/604.1'],
            [3, 'Mozilla/5.0 (iPhone; CPU iPhone OS 18_7 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/26.5 Mobile/15E148 Safari/604.1'],
            [3, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36 OPR/139.0.0.0 (Edition ms_store)'],
            [3, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36'],
            [2, 'Mozilla/5.0 (iPhone; CPU iPhone OS 27_2_0 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) CriOS/157.0.8103.37 Mobile/15E148 Safari/604.1'],
            [2, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0'],
            [2, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36'],
            [2, 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/26.2 Safari/605.1.15'],
            [2, 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Mobile Safari/537.36'],
            [2, 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Mobile Safari/537.36'],
            [2, 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Mobile Safari/537.36'],
        ];
    }

    public function test_los_ua_reales_de_produccion_se_clasifican_todos()
    {
        // La cobertura es el punto: un clasificador que devuelve `other`
        // sobre el trafico real no mejora nada respecto del bug que viene a
        // arreglar, donde la columna era `other` en el 100 % de las filas.
        $porCategoria = [];
        $sinClasificar = [];

        foreach ($this->uasDeProduccion() as [$requests, $ua]) {
            $cat = Device::desdeUserAgent($ua);
            $porCategoria[$cat] = ($porCategoria[$cat] ?? 0) + $requests;

            if ($cat === 'other') {
                $sinClasificar[] = $ua;
            }
        }

        $this->assertSame([], $sinClasificar, 'UA real sin clasificar');
        $this->assertSame(141, array_sum($porCategoria), 'la muestra cambio de tamano');

        // La mezcla medida. Se fija para que un cambio en la regla que mueva
        // trafico de una categoria a otra tenga que pasar por aca.
        // Medida, no estimada: 111 desktop (78,7 %) y 30 mobile (21,3 %).
        $this->assertSame(111, $porCategoria['desktop']);
        $this->assertSame(30, $porCategoria['mobile']);
        $this->assertArrayNotHasKey('tablet', $porCategoria);
    }

    /** @dataProvider casosDeCadaCategoria */
    public function test_cada_categoria($esperado, $ua, $nota)
    {
        $this->assertSame($esperado, Device::desdeUserAgent($ua), $nota);
    }

    public function casosDeCadaCategoria(): array
    {
        return [
            'windows chrome (el mas comun: 83 de 141)' => [
                'desktop',
                'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/157.0.0.0 Safari/537.36',
                'real',
            ],
            'mac safari' => [
                'desktop',
                'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/26.2 Safari/605.1.15',
                'real',
            ],
            'android telefono (UA reducido: version 10 y modelo K)' => [
                'mobile',
                'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/157.0.0.0 Mobile Safari/537.36',
                'real',
            ],
            'iphone safari' => [
                'mobile',
                'Mozilla/5.0 (iPhone; CPU iPhone OS 18_7 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/26.5 Mobile/15E148 Safari/604.1',
                'real',
            ],
            'ipad: SINTETICO, la muestra de produccion no trae ninguno' => [
                'tablet',
                'Mozilla/5.0 (iPad; CPU OS 18_7 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/26.5 Mobile/15E148 Safari/604.1',
                'el iPad dice Mobile: si mobile se preguntara primero, caeria ahi',
            ],
            'android tablet: SINTETICO, y es el caso que fija el ORDEN' => [
                'tablet',
                'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/157.0.0.0 Safari/537.36',
                'lo unico que lo separa de un telefono es que NO dice Mobile',
            ],
            'UA vacio NO es desktop' => [
                'other',
                '',
                'un default comodo mete lo no identificado en la categoria mas grande',
            ],
            'UA nulo NO es desktop' => ['other', null, 'idem'],
            'UA que no dice nada de ningun sistema' => ['other', 'curl/8.4.0', 'ni desktop ni movil'],
        ];
    }

    public function test_nunca_devuelve_un_valor_fuera_de_la_lista_cerrada()
    {
        // `EventRow::device()` ya mapea lo desconocido a `other`, asi que un
        // valor nuevo no corrompe la columna -- pero quedaria MUDO. Esto lo
        // hace ruidoso: una cuarta categoria que aparezca sola parte las
        // tasas, que es lo que dice el comentario de `DEVICES`.
        $validos = array_merge(EventRow::DEVICES, ['other']);

        foreach ($this->uasDeProduccion() as [, $ua]) {
            $this->assertContains(Device::desdeUserAgent($ua), $validos);
        }

        foreach ($this->casosDeCadaCategoria() as $caso) {
            $this->assertContains(Device::desdeUserAgent($caso[1]), $validos);
        }
    }
}
