<?php

namespace Cesargb\File\Rotate;

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
        $this->publishes([
            __DIR__.'/../config/rotate.php' => config_path('rotate.php'),
        ], 'config');

        if ($this->app->runningInConsole()) {
            $this->commands([
                Commands\Rotate::class,
                Commands\RotateFile::class,
            ]);
        }

        $this->app->booted(function () {
            if (config('rotate.schedule.enable', true)) {
                $schedule = $this->app->make(Schedule::class);

                $cronOldVersion = config('rotate.logs_rotate_schedule', '0 0 * * *');
                $cron = config('rotate.schedule.cron', $cronOldVersion);

                $schedule->command('rotate:logs')->cron($cron);
            }
        });
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
}
