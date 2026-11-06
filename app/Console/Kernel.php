<?php

namespace App\Console;

use App\Console\Commands\CleanTempFiles;
use App\Console\Commands\ReportFunnelDaily;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    protected $commands = [
        CleanTempFiles::class,
        ReportFunnelDaily::class,
    ];

    protected function schedule(Schedule $schedule)
    {
        $schedule->command('sofortpdf:clean-temp')->hourly();

        // `sofortpdf:sync-subscriptions` SE QUITO el 2026-11-06, y no por un
        // bug de una columna: usaba `stripe_price_id`, `stripe_subscription_id`
        // y `status`, tres columnas que la tabla COMPARTIDA `subscriptions` no
        // tiene. O sea no podia haber funcionado nunca, y nadie lo sabia
        // porque hasta el 2026-11-05 este server no tenia `schedule:run` en
        // ningun cron: el dia que se instalo, a las 03:00 del siguiente,
        // empezo a tirar una PDOException por dia.
        //
        // No se arreglo, se saco: el estado de una suscripcion lo sincroniza
        // el BO por webhooks de Stripe (invoice.paid,
        // customer.subscription.*), sin cron, y `subscriptions` es la tabla del
        // BO. El sitio no tiene una copia local que pueda divergir, asi que un
        // sync de este lado duplica al dueño del dato en vez de complementarlo.
        //
        // Si alguna vez hace falta algo de este lado, se escribe contra las
        // columnas que existen (`is_subscription_active`, `cancelled_at`) y con
        // un test. Lo fija tests/Unit/Console/ScheduledCommandsTest.php.

        // Embudo de adquisicion: manda al BO los contadores de AYER en
        // Europe/Bucharest. La zona se declara explicitamente porque la de la
        // app es UTC: a las 03:10 de Bucarest el dia que se reporta ya cerro.
        // Los cinco sitios estan escalonados de a 10 minutos (03:10 a 03:50)
        // para que un POST lento de uno no se cruce con otro.
        $schedule
            ->command('funnel:report')
            ->dailyAt('03:10')
            ->timezone('Europe/Bucharest')
            ->withoutOverlapping();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
