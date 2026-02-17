<?php

namespace Cesargb\LaravelLog\Test;

use Cesargb\LaravelLog\Events\RotateHasFailed;
use Cesargb\LaravelLog\Events\RotateWasSuccessful;
use Cesargb\LaravelLog\Helpers\Log as LogHelper;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Event;
use Monolog\Handler\StreamHandler;

class RotateTest extends TestCase
{
    public function test_no_rotate_if_file_logs_not_exits()
    {
        $resultCode = Artisan::call('rotate:logs');

        Event::assertDispatched(RotateWasSuccessful::class, 0);
        Event::assertDispatched(RotateHasFailed::class, 0);

        $this->assertEquals($resultCode, 0);
        $this->assertFileDoesNotExist(app()->storagePath().'/logs/laravel.log.1.gz');
    }

    public function test_no_rotate_if_file_logs_is_empty()
    {
        touch(app()->storagePath().'/logs/laravel.log');

        $resultCode = Artisan::call('rotate:logs');

        Event::assertDispatched(RotateWasSuccessful::class, 0);
        Event::assertDispatched(RotateHasFailed::class, 0);

        $this->assertEquals($resultCode, 0);
        $this->assertFileDoesNotExist(app()->storagePath().'/logs/laravel.log.1.gz');
    }

    public function test_it_not_rotate_logs_daily()
    {
        $this->app['config']->set('logging.default', 'daily');

        $this->writeLog();

        $files = LogHelper::getLaravelLogFiles();

        foreach ($files as $file) {
            $this->assertFileExists($file);
        }

        $resultCode = Artisan::call('rotate:logs');

        Event::assertDispatched(RotateWasSuccessful::class, 0);

        $this->assertEquals($resultCode, 0);

        foreach ($files as $file) {
            $this->assertFileDoesNotExist($file.'.1.gz');
        }
    }

    public function test_it_can_rotate_logs_custom_stream_file()
    {
        $this->app['config']->set('logging.channels.custom', [
            'driver' => 'monolog',
            'handler' => StreamHandler::class,
            'with' => [
                'stream' => app()->storagePath().'/logs/custom.log',
            ],
        ]);

        $this->app['config']->set('logging.default', 'custom');

        $this->writeLog();

        $this->assertFileExists(app()->storagePath().'/logs/custom.log');

        $resultCode = Artisan::call('rotate:logs');

        Event::assertDispatched(RotateWasSuccessful::class, 1);

        $this->assertEquals($resultCode, 0);
        $this->assertFileExists(app()->storagePath().'/logs/custom.log.1.gz');

        unlink(app()->storagePath().'/logs/custom.log.1.gz');
    }

    public function test_it_not_rotate_logs_custom_stream_std()
    {
        $this->app['config']->set('logging.channels.custom', [
            'driver' => 'monolog',
            'handler' => StreamHandler::class,
            'with' => [
                'stream' => 'php://stdout',
            ],
        ]);

        $this->app['config']->set('logging.default', 'custom');

        $resultCode = Artisan::call('rotate:logs');

        Event::assertDispatched(RotateWasSuccessful::class, 0);
        Event::assertDispatched(RotateHasFailed::class, 0);

        $this->assertEquals($resultCode, 0);
    }

    public function test_it_can_write_log_after_rotate()
    {
        $this->writeLog();

        $this->assertFileExists(app()->storagePath().'/logs/laravel.log');

        $resultCode = Artisan::call('rotate:logs');

        Event::assertDispatched(RotateWasSuccessful::class, 1);

        $this->assertEquals($resultCode, 0);
        $this->assertFileExists(app()->storagePath().'/logs/laravel.log.1.gz');

        $this->writeLog();

        $this->assertGreaterThan(0, filesize(app()->storagePath().'/logs/laravel.log'));
    }

    public function test_log_file_exits_if_truncate_enable()
    {
        $this->app['config']->set('rotate.truncate', true);
        $this->writeLog();

        $this->assertFileExists(app()->storagePath().'/logs/laravel.log');

        $resultCode = Artisan::call('rotate:logs');

        Event::assertDispatched(RotateWasSuccessful::class, 1);

        $this->assertEquals($resultCode, 0);
        $this->assertFileExists(app()->storagePath().'/logs/laravel.log.1.gz');
        $this->assertFileExists(app()->storagePath().'/logs/laravel.log');
    }

    public function test_log_file_does_not_exits_if_truncate_disable()
    {
        $this->app['config']->set('rotate.truncate', false);
        $this->writeLog();

        $this->assertFileExists(app()->storagePath().'/logs/laravel.log');

        $resultCode = Artisan::call('rotate:logs');

        Event::assertDispatched(RotateWasSuccessful::class, 1);

        $this->assertEquals($resultCode, 0);
        $this->assertFileExists(app()->storagePath().'/logs/laravel.log.1.gz');
        $this->assertFileDoesNotExist(app()->storagePath().'/logs/laravel.log');
    }

    public function test_rotate_foreing_files()
    {
        $file = storage_path('logs/foreing_file.log');

        file_put_contents($file, 'test');

        $this->app['config']->set('rotate.foreign_files', [
            $file,
        ]);

        $resultCode = Artisan::call('rotate:logs');

        Event::assertDispatched(RotateWasSuccessful::class, 1);

        $this->assertEquals($resultCode, 0);

        $this->assertFileExists(storage_path('logs/foreing_file.log.1.gz'));

        unlink(storage_path('logs/foreing_file.log.1.gz'));
    }

    public function test_no_rotate_if_file_is_smaller_than_min_size()
    {
        $this->app['config']->set('rotate.log_min_size', 1000);

        $logPath = app()->storagePath().'/logs/laravel.log';
        file_put_contents($logPath, 'Small log content');

        $this->assertFileExists($logPath);
        $this->assertLessThan(1000, filesize($logPath));

        $resultCode = Artisan::call('rotate:logs');

        Event::assertDispatched(RotateWasSuccessful::class, 0);
        Event::assertDispatched(RotateHasFailed::class, 0);

        $this->assertEquals($resultCode, 0);
        $this->assertFileDoesNotExist(app()->storagePath().'/logs/laravel.log.1.gz');
    }

    public function test_rotate_if_file_is_larger_than_or_equal_to_min_size()
    {
        $this->app['config']->set('rotate.log_min_size', 100);

        $logPath = app()->storagePath().'/logs/laravel.log';
        $largeContent = str_repeat('This is a log entry with some content. ', 10);
        file_put_contents($logPath, $largeContent);

        $this->assertFileExists($logPath);
        $this->assertGreaterThanOrEqual(100, filesize($logPath));

        $resultCode = Artisan::call('rotate:logs');

        Event::assertDispatched(RotateWasSuccessful::class, 1);

        $this->assertEquals($resultCode, 0);
        $this->assertFileExists(app()->storagePath().'/logs/laravel.log.1.gz');
    }

    public function test_rotate_when_min_size_is_zero()
    {
        $this->app['config']->set('rotate.log_min_size', 0);

        $logPath = app()->storagePath().'/logs/laravel.log';
        file_put_contents($logPath, 'Tiny');

        $this->assertFileExists($logPath);

        $resultCode = Artisan::call('rotate:logs');

        Event::assertDispatched(RotateWasSuccessful::class, 1);

        $this->assertEquals($resultCode, 0);
        $this->assertFileExists(app()->storagePath().'/logs/laravel.log.1.gz');
    }
}
