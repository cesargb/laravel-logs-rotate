<?php

namespace Cesargb\File\Rotate\Test\Handlers;

use Illuminate\Support\Facades\Event;
use Cesargb\File\Rotate\Test\TestCase;
use Illuminate\Support\Facades\Artisan;
use Cesargb\File\Rotate\Events\RotateWasSuccessful;

class RotativeHandlerTest extends TestCase
{
    /** @test **/
    public function it_can_rotate_logs()
    {
        $this->writeLog();

        $this->assertFileExists(app()->storagePath().'/logs/laravel.log');

        $resultCode = Artisan::call('logs:rotate');

        Event::assertDispatched(RotateWasSuccessful::class, 1);

        $this->assertEquals($resultCode, 0);
        $this->assertFileExists(app()->storagePath().'/logs/laravel.log.1.gz');
    }

    /** @test **/
    public function it_can_rotate_logs_withoutcompress()
    {
        $this->writeLog();

        $this->assertFileExists(app()->storagePath().'/logs/laravel.log');

        $this->app['config']->set('rotate.log_compress_files', false);

        $resultCode = Artisan::call('logs:rotate');

        Event::assertDispatched(RotateWasSuccessful::class, 1);

        $this->assertEquals($resultCode, 0);
        $this->assertFileExists(app()->storagePath().'/logs/laravel.log.1');
    }

    /** @test **/
    public function it_can_rotate_logs_with_maxfiles()
    {
        Event::fake();

        $this->app['config']->set('rotate.log_compress_files', true);

        for ($n = 0; $n < 10; $n++) {
            $this->writeLog();

            Artisan::call('logs:rotate');
        }

        Event::assertDispatched(RotateWasSuccessful::class, 10);

        $filesOld = glob(app()->storagePath().'/logs/laravel.log.*.gz');

        $this->assertFileExists(app()->storagePath().'/logs/laravel.log.1.gz');
        $this->assertFileExists(app()->storagePath().'/logs/laravel.log.2.gz');
        $this->assertFileExists(app()->storagePath().'/logs/laravel.log.3.gz');
        $this->assertFileExists(app()->storagePath().'/logs/laravel.log.4.gz');
        $this->assertFileExists(app()->storagePath().'/logs/laravel.log.5.gz');
        $this->assertFalse(file_exists(app()->storagePath().'/logs/laravel.log.6.gz'));
    }
}
