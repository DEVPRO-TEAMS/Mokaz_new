<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    // protected function schedule(Schedule $schedule): void
    // {
    //     // $schedule->command('inspire')->hourly();
    // }

    protected function schedule(Schedule $schedule)
    {
        // Nettoyer les données analytiques anciennes tous les dimanches à 3h du matin
        $schedule->command('analytics:cleanup --days=365 --force')
                ->sundays()
                ->at('03:00')
                ->withoutOverlapping()
                ->appendOutputTo(storage_path('logs/analytics-cleanup.log'));
        
        // Corriger les données problématiques tous les lundis à 2h du matin
        $schedule->command('analytics:fix-data --clean-sessions --fix-durations --force')
                ->mondays()
                ->at('02:00')
                ->withoutOverlapping()
                ->appendOutputTo(storage_path('logs/analytics-fix.log'));
    }


    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
