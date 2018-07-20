<?php

namespace Cesargb\File\Rotate\Test;

use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Artisan;
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
    }
}
