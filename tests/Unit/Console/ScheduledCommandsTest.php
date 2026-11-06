<?php

namespace Tests\Unit\Console;

use Illuminate\Console\Scheduling\Schedule;
use Tests\TestCase;

/**
 * Fija EXACTAMENTE que comandos quedan agendados.
 *
 * Por que existe este test, y es la leccion del 2026-11-06: hasta el
 * 2026-11-05 este server NO tenia `php artisan schedule:run` en ningun cron,
 * asi que todo lo que estaba en `schedule()` **nunca habia corrido**. Cuando
 * se instalo el cron, a las 03:00 del dia siguiente `sofortpdf:sync-subscriptions`
 * tiro `Unknown column 'stripe_price_id'`: usaba tres columnas que la tabla
 * compartida `subscriptions` no tiene, o sea no podia haber funcionado nunca.
 *
 * Un comando agendado que nadie corre no es codigo muerto inofensivo: es
 * codigo que se va a ejecutar el dia que alguien arregle la infraestructura, y
 * para entonces nadie se acuerda de que estaba ahi. Este test obliga a que
 * agregar o sacar un agendado sea un acto deliberado.
 *
 * Lo que este test NO prueba: que cada comando corra sin estallar. Los dos
 * tocan red o disco. Eso lo cubre que su salida vaya a un archivo y no a
 * /dev/null, que es la otra mitad de la misma leccion.
 */
class ScheduledCommandsTest extends TestCase
{
    public function test_los_comandos_agendados_son_exactamente_estos()
    {
        $agendados = [];

        foreach (app(Schedule::class)->events() as $evento) {
            // El comando viene como la linea entera de artisan; se queda solo
            // con el nombre, que es lo que identifica al agendado.
            if (preg_match("/artisan'? (?:[^ ]+ )?'?([a-z0-9:_-]+)/i", (string) $evento->command, $m)) {
                $agendados[] = $m[1];
            }
        }

        sort($agendados);

        // `funnel:sweep` entro con cristian-107: la retencion de
        // funnel_events. Agregar un agendado tiene que romper este test a
        // proposito -- un comando que se agenda sin que nadie lo note es
        // codigo que se ejecuta el dia que alguien arregla la infra, cuando ya
        // nadie se acuerda de que estaba ahi (pasado el 2026-11-06 con
        // sync-subscriptions).
        $this->assertSame(['funnel:report', 'funnel:sweep', 'sofortpdf:clean-temp'], $agendados);
    }

    public function test_sync_subscriptions_no_vuelve_a_agendarse()
    {
        // Explicito y aparte del test de arriba para que el motivo quede en el
        // nombre: el estado de una suscripcion lo sincroniza el BO por
        // webhooks de Stripe (invoice.paid, customer.subscription.*), sin
        // cron. `subscriptions` es la tabla COMPARTIDA del BO -- el sitio no
        // tiene una copia local que pueda divergir. Un sync del lado del sitio
        // duplica al dueño del dato en vez de complementarlo.
        foreach (app(Schedule::class)->events() as $evento) {
            $this->assertStringNotContainsString('sync-subscriptions', (string) $evento->command);
        }
    }
}
