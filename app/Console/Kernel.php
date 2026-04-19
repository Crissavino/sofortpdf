<?php

namespace App\Console;

use App\Console\Commands\CleanTempFiles;
use App\Console\Commands\SyncSubscriptions;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    protected $commands = [
        CleanTempFiles::class,
        SyncSubscriptions::class,
    ];

    protected function schedule(Schedule $schedule)
    {
        $schedule->command('sofortpdf:clean-temp')->hourly();
        $schedule->command('sofortpdf:sync-subscriptions')->dailyAt('03:00');
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
