<?php

namespace Tests\Unit\Funnel;

use App\Console\Commands\ReportFunnelDaily;
use App\Src\Funnel\EventRow;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

/**
 * LOS DOS MAPAS DEL AGREGADOR, O NINGUNO.
 *
 * `ReportFunnelDaily` tiene dos mapas evento -> paso porque tiene dos fuentes:
 * `PASOS` lee el log rotado y `PASOS_TABLA` lee `funnel_events`. Cual manda lo
 * decide el flag (`config('funnel.enabled')`), asi que un paso mapeado en uno
 * solo FUNCIONA hasta el dia del flip y despues desaparece: sin error, sin
 * excepcion y sin una fila menos en ningun log. Es la falla muda que dejo el
 * embudo once dias roto en noviembre de 2026, y es exactamente la que el
 * ticket darryl-253 pedia no repetir.
 *
 * Nada mas aseveraba esa simetria: los dos mapas son `private const` y el
 * unico test que los tocaba no existia. Por eso este archivo usa Reflection en
 * vez de hacerlos publicos -- el acoplamiento tiene que estar del lado del
 * test, no de la API del comando.
 *
 * Puro: ni DB ni Laravel booteado.
 */
class PasosDelAgregadorTest extends TestCase
{
    /** @return array<string,string> */
    private function mapa(string $nombre): array
    {
        $constante = (new ReflectionClass(ReportFunnelDaily::class))
            ->getReflectionConstant($nombre);

        $this->assertNotFalse($constante, "ReportFunnelDaily no tiene la constante {$nombre}");

        return (array) $constante->getValue();
    }

    public function test_los_dos_mapas_cubren_los_mismos_pasos_del_bo()
    {
        // La igualdad es sobre los PASOS (los valores), no sobre los eventos
        // (las claves): las claves son los dos vocabularios distintos, que es
        // justo la razon por la que hay dos mapas. Lo que no puede diferir es
        // que key del BO sabe producir cada fuente.
        $delArchivo = array_values($this->mapa('PASOS'));
        $deLaTabla = array_values($this->mapa('PASOS_TABLA'));

        sort($delArchivo);
        sort($deLaTabla);

        $this->assertSame(
            $delArchivo,
            $deLaTabla,
            'un paso mapeado en una sola fuente desaparece el dia del flip, en silencio'
        );
    }

    public function test_pay_clicked_esta_en_las_dos_fuentes()
    {
        // El paso del ticket, nombrado. El test de arriba caza la clase; este
        // caza el caso, para que un dia en que los dos mapas pierdan
        // `pay_clicked` a la vez siga dando rojo.
        $this->assertSame('pay_clicked', $this->mapa('PASOS')['pay_clicked'] ?? null);
        $this->assertSame('pay_clicked', $this->mapa('PASOS_TABLA')['pay_clicked'] ?? null);
    }

    public function test_lo_que_lee_la_tabla_son_eventos_canonicos()
    {
        // `PASOS_TABLA` consulta la columna `event` de `funnel_events`, donde
        // solo entran eventos de la taxonomia (`EventRow::build()` rechaza el
        // resto). Una clave que no sea canonica daria 0 para siempre, y un 0
        // se lee como "no paso nada" y no como "mapeo roto".
        foreach (array_keys($this->mapa('PASOS_TABLA')) as $evento) {
            $this->assertContains($evento, EventRow::EVENTOS, "PASOS_TABLA['{$evento}']");
        }
    }

    public function test_lo_que_lee_el_archivo_son_nombres_que_el_cliente_manda()
    {
        // La contracara: `PASOS` grepea el log de `/api/log/event`, donde el
        // nombre es el que escribe el blade. Si una clave de aca no esta en
        // `DEL_CLIENTE`, es un nombre que ningun beacon emite.
        foreach (array_keys($this->mapa('PASOS')) as $evento) {
            $this->assertArrayHasKey(
                $evento,
                EventRow::DEL_CLIENTE,
                "PASOS['{$evento}'] no es un nombre que este sitio emita"
            );
        }
    }
}
