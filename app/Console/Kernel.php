<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('tasks:mark-overdue')->everyFiveMinutes();
        $schedule->command('tasks:winners-by-invites')->everyTenMinutes();
        $schedule->command('tasks:remind-expiration')->hourly();
        $schedule->command('tasks:remove-managers')->everyThirtyMinutes();
        $schedule->command('invites:remove-inactive')->everyFiveMinutes();
        $schedule->command('coins:update-rates')->daily();
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
