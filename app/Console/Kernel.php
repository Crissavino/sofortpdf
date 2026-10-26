<?php

namespace App\Console;

use App\Console\Commands\CleanTempFiles;
use App\Console\Commands\ReportFunnelDaily;
use App\Console\Commands\SyncSubscriptions;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    protected $commands = [
        CleanTempFiles::class,
        ReportFunnelDaily::class,
        SyncSubscriptions::class,
    ];

    protected function schedule(Schedule $schedule)
    {
        $schedule->command('sofortpdf:clean-temp')->hourly();
        $schedule->command('sofortpdf:sync-subscriptions')->dailyAt('03:00');

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
