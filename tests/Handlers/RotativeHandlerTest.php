<?php

namespace Cesargb\LaravelLog\Test\Handlers;

use Cesargb\LaravelLog\Events\RotateWasSuccessful;
use Cesargb\LaravelLog\Test\TestCase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Event;

class RotativeHandlerTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        $this->app['config']->set('rotate.log_max_files', 5);
    }

    public function testItCanRotateLogs()
    {
        $this->writeLog();

        $this->assertGreaterThan(0, filesize(app()->storagePath().'/logs/laravel.log'));

        $resultCode = Artisan::call('rotate:logs');

        Event::assertDispatched(RotateWasSuccessful::class, 1);

        $this->assertEquals($resultCode, 0);
        $this->assertEquals(filesize(app()->storagePath().'/logs/laravel.log'), 0);
        $this->assertFileExists(app()->storagePath().'/logs/laravel.log.1.gz');
    }

    public function testItCanRotateLogsWithoutcompress()
    {
        $this->writeLog();

        $this->assertGreaterThan(0, filesize(app()->storagePath().'/logs/laravel.log'));

        $this->app['config']->set('rotate.log_compress_files', false);

        $resultCode = Artisan::call('rotate:logs');

        Event::assertDispatched(RotateWasSuccessful::class, 1);

        $this->assertEquals($resultCode, 0);
        $this->assertEquals(filesize(app()->storagePath().'/logs/laravel.log'), 0);
        $this->assertFileExists(app()->storagePath().'/logs/laravel.log.1');
    }

    public function testItCanRotateLogsWithMaxfiles()
    {
        Event::fake();

        $this->app['config']->set('rotate.log_compress_files', true);

        for ($n = 0; $n < 10; $n++) {
            $this->writeLog();

            $this->assertGreaterThan(0, filesize(app()->storagePath().'/logs/laravel.log'));

            Artisan::call('rotate:logs');
        }

        Event::assertDispatched(RotateWasSuccessful::class, 10);

        $this->assertEquals(filesize(app()->storagePath().'/logs/laravel.log'), 0);

        $this->assertFileExists(app()->storagePath().'/logs/laravel.log.1.gz');
        $this->assertFileExists(app()->storagePath().'/logs/laravel.log.2.gz');
        $this->assertFileExists(app()->storagePath().'/logs/laravel.log.3.gz');
        $this->assertFileExists(app()->storagePath().'/logs/laravel.log.4.gz');
        $this->assertFileExists(app()->storagePath().'/logs/laravel.log.5.gz');
        $this->assertFileDoesNotExist(app()->storagePath().'/logs/laravel.log.6.gz');
    }
}
