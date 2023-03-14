<?php

namespace App\Console;

use App\Models\Instance;
use App\Models\InstanceProcess;
use Carbon\Carbon;
use DateTimeZone;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Process;

class Kernel extends ConsoleKernel
{
    /**
     * Set the timezone that should be used by default for scheduled events to UTC.
     */
    protected function scheduleTimezone(): DateTimeZone|string|null
    {
        return 'UTC';
    }

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        /**
         * ATTENTION
         *
         * In the `local` environment, where this Docker setup is used, some commands may be unable to work as expected since
         * it is running various commands in various Docker containers (e.g. `schedule` container adds a job to the `queue`,
         * but the `queue` container actually runs it, thus the `schedule` container can not see the process ID of it).
         */

        // Only for debugging purposes in the local Docker setup
        $schedule->call(function () {
            Log::debug('Your `APP_ENV` is set to `local`. Current datetime in UTC: '.Carbon::now());
        })->environments(['local'])->name('docker:debug')->everyFifteenMinutes();

        // INSTANCES: Cleanup dead processes
        $schedule->call(function () {
            foreach (InstanceProcess::all(['process_id']) as $process) {
                if (file_exists("/proc/$process->process_id")) {
                    continue;
                }

                Log::info("Cleaning up dead PID $process->process_id from database...");

                $process->delete();
            }
        })->environments(['staging', 'production'])->name('instances:cleanup-dead-processes')->everyMinute();

        // INSTANCES: Autostart
        $schedule->call(function () {
            $instances_with_autostart = Instance::where(['autostart_enabled' => true])->get(['id']);

            foreach ($instances_with_autostart as $instance) {
                if (is_null($instance->process)) {
                    Log::info("Starting instance $instance->id since autostart is enabled...");
                    Process::start('php '.base_path()."/artisan instance:start-teamspeak-bot $instance->id --background");
                }
            }
        })->environments(['staging', 'production'])->name('instances:autostart')->everyFiveMinutes();
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
