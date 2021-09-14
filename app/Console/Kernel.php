<?php

namespace App\Console;

use App\Models\Setting;
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
        //
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
         $schedule->command('queue:work --stop-when-empty')->everyMinute()->withoutOverlapping();

         switch(Setting::where("setting", "=", "scanning_frequency")->first()->value) {
             case "Every 15 Minutes":
                 $schedule->command("Scan:Library")->everyFifteenMinutes()->withoutOverlapping();

                 break;
             case "Every 30 Minutes":
                 $schedule->command("Scan:Library")->everyThirtyMinutes()->withoutOverlapping();

                 break;
             case "Every Hour":
                 $schedule->command("Scan:Library")->hourly()->withoutOverlapping();

                 break;
             case "Every 3 Hours":
                 $schedule->command("Scan:Library")->everyThreeHours()->withoutOverlapping();

                 break;
             case "Every 6 Hours":
                 $schedule->command("Scan:Library")->everySixHours()->withoutOverlapping();

                 break;
             case "Every 12 Hours":
                 $schedule->command("Scan:Library")->twiceDaily(1, 13)->withoutOverlapping();

                 break;
             case "Every 24 Hours":
                 $schedule->command("Scan:Library")->daily()->withoutOverlapping();

                 break;
         }

        $schedule->command('Import:OPDS')->daily();

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
