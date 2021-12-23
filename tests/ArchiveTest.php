<?php

namespace Cesargb\LaravelLog\Test;

use Cesargb\LaravelLog\Events\RotateWasSuccessful;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Event;

class ArchiveTest extends TestCase
{
    public function testItCanRotateLogsInArchiveDir()
    {
        $archive_folder = app()->storagePath().'/logs/archive';

        $this->app['config']->set('rotate.archive_dir', $archive_folder);

        $this->writeLog();

        $this->assertFileExists(app()->storagePath().'/logs/laravel.log');

        $resultCode = Artisan::call('rotate:logs');

        Event::assertDispatched(RotateWasSuccessful::class, 1);

        $this->assertEquals($resultCode, 0);
        $this->assertFileExists($archive_folder.'/laravel.log.1.gz');

        unlink($archive_folder.'/laravel.log.1.gz');

        rmdir($archive_folder);
    }

    public function testItCanRotateLogsInArchiveDirRelative()
    {
        $archive_folder = 'archives';

        $this->app['config']->set('rotate.archive_dir', $archive_folder);

        $this->writeLog();

        $this->assertFileExists(app()->storagePath().'/logs/laravel.log');

        $resultCode = Artisan::call('rotate:logs');

        Event::assertDispatched(RotateWasSuccessful::class, 1);

        $this->assertEquals($resultCode, 0);
        $this->assertFileExists(app()->storagePath().'/logs/'.$archive_folder.'/laravel.log.1.gz');

        unlink(app()->storagePath().'/logs/'.$archive_folder.'/laravel.log.1.gz');

        rmdir(app()->storagePath().'/logs/'.$archive_folder);
    }
}
