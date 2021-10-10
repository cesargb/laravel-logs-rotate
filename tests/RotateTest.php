<?php

namespace Cesargb\File\Rotate\Test;

use Cesargb\File\Rotate\Events\RotateIsNotNecessary;
use Cesargb\File\Rotate\Events\RotateWasSuccessful;
use Cesargb\File\Rotate\Helpers\Log as LogHelper;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Event;
use Monolog\Handler\StreamHandler;

class RotateTest extends TestCase
{
    public function testNoRotateIfFileLogsNotExits()
    {
        $resultCode = Artisan::call('rotate:logs');

        Event::assertDispatched(RotateIsNotNecessary::class, 1);

        $this->assertEquals($resultCode, 0);
        $this->assertFileNotExists(app()->storagePath().'/logs/laravel.log.1.gz');
    }

    public function testNoRotateIfFileLogsIsEmpty()
    {
        touch(app()->storagePath().'/logs/laravel.log');

        $resultCode = Artisan::call('rotate:logs');

        Event::assertDispatched(RotateIsNotNecessary::class, 1);

        $this->assertEquals($resultCode, 0);
        $this->assertFileNotExists(app()->storagePath().'/logs/laravel.log.1.gz');
    }

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

    public function testItNotRotateLogsDaily()
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
            $this->assertFileNotExists($file.'.1.gz');
        }
    }

    public function testItCanRotateLogsCustomStreamFile()
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

    public function testItNotRotateLogsCustomStreamStd()
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
        Event::assertDispatched(RotateIsNotNecessary::class, 1);

        $this->assertEquals($resultCode, 0);
    }

    public function testItCanWriteLogAfterRotate()
    {
        $this->writeLog();

        $this->assertFileExists(app()->storagePath().'/logs/laravel.log');

        $resultCode = Artisan::call('rotate:logs');

        Event::assertDispatched(RotateWasSuccessful::class, 1);

        $this->assertEquals($resultCode, 0);
        $this->assertFileExists(app()->storagePath().'/logs/laravel.log');
        $this->assertFileExists(app()->storagePath().'/logs/laravel.log.1.gz');

        $this->writeLog();

        $this->assertGreaterThan(0, filesize(app()->storagePath().'/logs/laravel.log'));
    }

    public function testRotateForeingFiles()
    {
        $file = storage_path('logs/foreing_file.log');

        file_put_contents($file, 'test');

        $this->app['config']->set('rotate.foreing_files', [
            $file,
        ]);

        $resultCode = Artisan::call('rotate:logs');

        Event::assertDispatched(RotateWasSuccessful::class, 1);

        $this->assertEquals($resultCode, 0);

        $this->assertFileExists($file);
        $this->assertFileExists(storage_path('logs/foreing_file.log.1.gz'));

        unlink($file);
        unlink(storage_path('logs/foreing_file.log.1.gz'));
    }
}
