<?php

namespace App\Console;

use App\Jobs\RescanLibrary;
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

         switch(Setting::where("setting", "=", "scanning_frequency")->first()->value) {
             case "Every 15 Minutes":
                 $schedule->job(new RescanLibrary())->everyFifteenMinutes();

                 break;
             case "Every 30 Minutes":
                 $schedule->job(new RescanLibrary())->everyThirtyMinutes();

                 break;
             case "Every Hour":
                 $schedule->job(new RescanLibrary())->hourly();

                 break;
             case "Every 3 Hours":
                 $schedule->job(new RescanLibrary())->everyThreeHours();

                 break;
             case "Every 6 Hours":
                 $schedule->job(new RescanLibrary())->everySixHours();

                 break;
             case "Every 12 Hours":
                 $schedule->job(new RescanLibrary())->twiceDaily(1, 13);

                 break;
             case "Every 24 Hours":
                 $schedule->job(new RescanLibrary())->daily();

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
