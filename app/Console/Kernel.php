<?php

namespace App\Console;

use App\Console\Commands\CleanTempFiles;
use App\Console\Commands\SyncSubscriptions;
use App\Console\Commands\TrialReminder;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        CleanTempFiles::class,
        TrialReminder::class,
        SyncSubscriptions::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('sofortpdf:clean-temp')->hourly();
        $schedule->command('sofortpdf:trial-reminder')->dailyAt('10:00');
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
