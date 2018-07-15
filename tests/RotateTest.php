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

        $filesOld = glob(app()->storagePath().'/logs/*');

        foreach ($filesOld as $f) {
            unlink($f);
        }
    }

    /** @test **/
    public function it_can_rotate_logs()
    {
        Log::info('test');

	    $logs = glob(app()->storagePath().'/logs/*.log');
	    foreach($logs as $log){
		    $this->assertFileExists($log);
	    }

        $resultCode = Artisan::call('logs:rotate');

        $this->assertEquals($resultCode, 0);

	    foreach($logs as $log){
		    $this->assertFileExists($log.'.1.gz');
	    }
    }

    /** @test **/
    public function it_can_rotate_logs_withoutcompress()
    {
        Log::info('test');

	    $logs = glob(app()->storagePath().'/logs/*.log');
	    foreach($logs as $log){
		    $this->assertFileExists($log);
	    }

        $this->app['config']->set('rotate.log_compress_files', false);

        $resultCode = Artisan::call('logs:rotate');

        $this->assertEquals($resultCode, 0);
	    foreach($logs as $log){
		    $this->assertFileExists($log.'.1');
	    }

    }

    /** @test **/
    public function it_can_rotate_logs_with_maxfiles()
    {
	    $this->app['config']->set('rotate.log_compress_files', true);

	    $logs = glob(app()->storagePath().'/logs/*.log');

	    for ($n = 0; $n < 10; $n++) {
		    foreach($logs as $log){
			    file_put_contents($log, 'test');
		    }
		    Artisan::call('logs:rotate');
	    }

	    $filesOld = glob(app()->storagePath().'/logs/*.log.*.gz');
	    foreach($logs as $log){
	    	for($i=1;$i<=5;$i++){
			    $this->assertFileExists($log.'1.gz');
		    }
		    $this->assertFalse(file_exists($log.'.6.gz'));
	    }
    }
}
