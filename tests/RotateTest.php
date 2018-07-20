<?php

namespace Cesargb\File\Rotate\Test;

use Monolog\Handler\StreamHandler;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Artisan;
use Cesargb\File\Rotate\Helpers\Log as LogHelper;
use Cesargb\File\Rotate\Events\RotateHasFailed;
use Cesargb\File\Rotate\Events\RotateWasSuccessful;
use Cesargb\File\Rotate\Events\RotateIsNotNecessary;

class RotateTest extends TestCase
{
    /** @test **/
    public function no_rotate_if_file_logs_not_exits()
    {
        $resultCode = Artisan::call('logs:rotate');

        Event::assertDispatched(RotateIsNotNecessary::class, 1);

        $this->assertEquals($resultCode, 0);
        $this->assertFalse(file_exists(app()->storagePath().'/logs/laravel.log.1.gz'));
    }

    /** @test **/
    public function no_rotate_if_file_logs_is_empty()
    {
        touch(app()->storagePath().'/logs/laravel.log');

        $resultCode = Artisan::call('logs:rotate');

        Event::assertDispatched(RotateIsNotNecessary::class, 1);

        $this->assertEquals($resultCode, 0);
        $this->assertFalse(file_exists(app()->storagePath().'/logs/laravel.log.1.gz'));
    }

    /** @test **/
    public function it_can_rotate_logs_in_archive_dir()
    {
        $archive_folder = app()->storagePath().'/logs/archive';

        $this->app['config']->set('rotate.archive_dir', $archive_folder);

        $this->writeLog();

        $this->assertFileExists(app()->storagePath().'/logs/laravel.log');

        $resultCode = Artisan::call('logs:rotate');

        Event::assertDispatched(RotateWasSuccessful::class, 1);

        $this->assertEquals($resultCode, 0);
        $this->assertFileExists($archive_folder.'/laravel.log.1.gz');

        unlink($archive_folder.'/laravel.log.1.gz');

        rmdir($archive_folder);
    }

    /** @test **/
    public function it_can_rotate_logs_in_archive_dir_relative()
    {
        $archive_folder = 'archives';

        $this->app['config']->set('rotate.archive_dir', $archive_folder);

        $this->writeLog();

        $this->assertFileExists(app()->storagePath().'/logs/laravel.log');

        $resultCode = Artisan::call('logs:rotate');

        Event::assertDispatched(RotateWasSuccessful::class, 1);

        $this->assertEquals($resultCode, 0);
        $this->assertFileExists(app()->storagePath().'/logs/'.$archive_folder.'/laravel.log.1.gz');

        unlink(app()->storagePath().'/logs/'.$archive_folder.'/laravel.log.1.gz');

        rmdir(app()->storagePath().'/logs/'.$archive_folder);
    }

    /** @test **/
    public function it_can_rotate_logs_daily()
    {
        $this->app['config']->set('app.log', 'daily');
        $this->app['config']->set('logging.default', 'daily');

        $this->writeLog();

        $files = LogHelper::getLaravelLogFiles();

        foreach ($files as $file) {
            $this->assertFileExists($file);
        }

        $resultCode = Artisan::call('logs:rotate');

        Event::assertDispatched(RotateWasSuccessful::class, 0);
        Event::assertDispatched(RotateIsNotNecessary::class, 0);

        $this->assertEquals($resultCode, 0);

        foreach ($files as $file) {
            $this->assertFileNotExists($file.'.1.gz');
        }

    }

    /** @test **/
    public function it_can_rotate_logs_custom()
    {
        if (LogHelper::laravelVersion() == '5.5') {
            $this->assertTrue(true);
        } else {
            $this->app['config']->set('logging.channels.custom', [
                'driver' => 'monolog',
                'handler' => StreamHandler::class,
                'with' => [
                    'stream' => app()->storagePath().'/logs/custom.log',
                ]
            ]);

            $this->app['config']->set('logging.default', 'custom');


            $this->writeLog();

            $this->assertFileExists(app()->storagePath().'/logs/custom.log');

            $resultCode = Artisan::call('logs:rotate');

            Event::assertDispatched(RotateWasSuccessful::class, 1);

            $this->assertEquals($resultCode, 0);
            $this->assertFileExists(app()->storagePath().'/logs/custom.log.1.gz');

            unlink(app()->storagePath().'/logs/custom.log.1.gz');
        }
    }

    /** @test **/
    public function it_can_rotate_logs_custom2()
    {
        if (LogHelper::laravelVersion() == '5.5') {
            $this->assertTrue(true);
        } else {
            $this->app['config']->set('logging.channels.custom', [
                'driver' => 'monolog',
                'handler' => StreamHandler::class,
                'with' => [
                    'stream' => 'php://stdout',
                ]
            ]);

            $this->app['config']->set('logging.default', 'custom');

            $resultCode = Artisan::call('logs:rotate');

            Event::assertDispatched(RotateWasSuccessful::class, 0);
            Event::assertDispatched(RotateIsNotNecessary::class, 1);

            $this->assertEquals($resultCode, 0);
        }
    }
}
