<?php

namespace Cesargb\LaravelLog;

use Cesargb\LaravelLog\Commands\RotateCommand;
use Cesargb\LaravelLog\Commands\RotateFileCommand;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\ServiceProvider;

class RotateServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/rotate.php' => $this->app->configPath('rotate.php'),
            ], 'config');

            $this->commands([
                RotateCommand::class,
                RotateFileCommand::class,
            ]);

            $this->registerSchedule();
        }
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/rotate.php', 'rotate');
    }

    private function registerSchedule()
    {
        if (!config('rotate.schedule.enable', true)) {
            return;
        }

        $this->callAfterResolving(Schedule::class, function (Schedule $schedule) {
            $cronOldVersion = config('rotate.logs_rotate_schedule', '0 0 * * *');
            $cron = config('rotate.schedule.cron', $cronOldVersion);

            $schedule->command('rotate:logs')->cron($cron);
        });
    }
}
