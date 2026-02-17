<?php

namespace Cesargb\LaravelLog\Test;

use Cesargb\LaravelLog\Helpers\Log as LogHelper;
use Cesargb\LaravelLog\RotateServiceProvider;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;
use Orchestra\Testbench\TestCase as Orchestra;

abstract class TestCase extends Orchestra
{
    protected $tmpDir;

    protected function setUp(): void
    {
        parent::setUp();

        Event::fake();

        $this->app['config']->set('app.log', 'single');
        $this->app['config']->set('logging.default', 'single');
        $this->app['config']->set('rotate.log_compress_files', true);

        $this->tmpDir = dirname(__FILE__).'/tmp';

        $this->cleanLogs();

        if (! file_exists($this->tmpDir)) {
            mkdir($this->tmpDir);
        }
    }

    protected function getPackageProviders($app)
    {
        return [
            RotateServiceProvider::class,
        ];
    }

    protected function writeLog()
    {
        Log::info('test');
    }

    protected function cleanLogs()
    {
        foreach (LogHelper::getLaravelLogFiles() as $fileLog) {
            $filesToRemove = glob(dirname($fileLog).'/*');

            foreach ($filesToRemove as $f) {
                if (is_file($f) && ! is_dir($f)) {
                    unlink($f);
                }
            }
        }

        $filesToRemove = glob($this->tmpDir.'/*');

        foreach ($filesToRemove as $f) {
            if (is_file($f) && ! is_dir($f)) {
                unlink($f);
            }
        }

        $filesToRemove = glob($this->tmpDir.'/archive/*');

        foreach ($filesToRemove as $f) {
            if (is_file($f) && ! is_dir($f)) {
                unlink($f);
            }
        }
    }
}
