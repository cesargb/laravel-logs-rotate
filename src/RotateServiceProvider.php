<?php

namespace Cesargb\File\Rotate;

use Illuminate\Support\ServiceProvider;
use Illuminate\Console\Scheduling\Schedule;

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
            __DIR__.'/config/rotate.php' => config_path('rotate.php'),
        ], 'config');

        if ($this->app->runningInConsole()) {
            $this->commands([
                Commands\Rotate::class,
            ]);
        }

        $this->app->booted(function () {
            if (config('update.scheduler.check.enable', true)) {
                $schedule = $this->app->make(Schedule::class);

                $schedule->command('logs:rotate')->cron(config('rotate.logs_rotate_schedule', '0 0 * * *'));
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
