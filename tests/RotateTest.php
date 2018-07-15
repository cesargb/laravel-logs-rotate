<?php

namespace Cesargb\File\Rotate\Test;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;

class RotateTest extends TestCase
{
    public function setUp()
    {
        parent::setUp();

        $this->app['config']->set('app.log', 'single');
        $this->app['config']->set('rotate.log_compress_files', true);
        $this->app['config']->set('rotate.log_max_files', 5);

        $filesOld = glob(app()->storagePath().'/logs/{laravel,worker}*',GLOB_BRACE);

        foreach ($filesOld as $f) {
            unlink($f);
        }
    }

    /** @test **/
    public function it_can_rotate_logs()
    {
        Log::info('test');

        $this->assertFileExists(app()->storagePath().'/logs/laravel.log');
	    $this->assertFileExists(app()->storagePath().'/logs/worker.log');

        $resultCode = Artisan::call('logs:rotate');

        $this->assertEquals($resultCode, 0);
        $this->assertFileExists(app()->storagePath().'/logs/laravel.log.1.gz');
	    $this->assertFileExists(app()->storagePath().'/logs/worker.log.1.gz');

    }

    /** @test **/
    public function it_can_rotate_logs_withoutcompress()
    {
        Log::info('test');

        $this->assertFileExists(app()->storagePath().'/logs/laravel.log');
	    $this->assertFileExists(app()->storagePath().'/logs/worker.log');

        $this->app['config']->set('rotate.log_compress_files', false);

        $resultCode = Artisan::call('logs:rotate');

        $this->assertEquals($resultCode, 0);
        $this->assertFileExists(app()->storagePath().'/logs/laravel.log.1');
	    $this->assertFileExists(app()->storagePath().'/logs/worker.log.1');
    }

    /** @test **/
    public function it_can_rotate_logs_with_maxfiles()
    {
        $this->app['config']->set('rotate.log_compress_files', true);

        for ($n = 0; $n < 10; $n++) {
            file_put_contents(app()->storagePath().'/logs/laravel.log', 'test');
	        file_put_contents(app()->storagePath().'/logs/worker.log', 'test');
            Artisan::call('logs:rotate');
        }

        $filesOld = glob(app()->storagePath().'/logs/{laravel,worker}.log.*.gz',GLOB_BRACE);

        $this->assertFileExists(app()->storagePath().'/logs/laravel.log.1.gz');
        $this->assertFileExists(app()->storagePath().'/logs/laravel.log.2.gz');
        $this->assertFileExists(app()->storagePath().'/logs/laravel.log.3.gz');
        $this->assertFileExists(app()->storagePath().'/logs/laravel.log.4.gz');
        $this->assertFileExists(app()->storagePath().'/logs/laravel.log.5.gz');
        $this->assertFalse(file_exists(app()->storagePath().'/logs/laravel.log.6.gz'));

	    $this->assertFileExists(app()->storagePath().'/logs/worker.log.1.gz');
	    $this->assertFileExists(app()->storagePath().'/logs/worker.log.2.gz');
	    $this->assertFileExists(app()->storagePath().'/logs/worker.log.3.gz');
	    $this->assertFileExists(app()->storagePath().'/logs/worker.log.4.gz');
	    $this->assertFileExists(app()->storagePath().'/logs/worker.log.5.gz');
	    $this->assertFalse(file_exists(app()->storagePath().'/logs/worker.log.6.gz'));

    }
}
