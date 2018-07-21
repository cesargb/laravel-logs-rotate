<?php

namespace Cesargb\File\Rotate\Test\Commands;

use Monolog\Handler\StreamHandler;
use Cesargb\File\Rotate\Test\TestCase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Artisan;
use Cesargb\File\Rotate\Helpers\Log as LogHelper;
use Cesargb\File\Rotate\Events\RotateWasSuccessful;
use Cesargb\File\Rotate\Events\RotateIsNotNecessary;

class RotateFileTest extends TestCase
{

    public function setUp()
    {
        parent::setUp();

        file_put_contents($this->tmpDir.'/file1', 'test');
        file_put_contents($this->tmpDir.'/file2', 'test');
    }

    /** @test **/
    public function it_can_rotate_file()
    {
        $file1 = $this->tmpDir.'/file1';
        $file2 = $this->tmpDir.'/file2';

        $resultCode = Artisan::call('rotate:files', [
            '--file'    => [ $file1, $file2 ],
        ]);

        Event::assertDispatched(RotateWasSuccessful::class, 2);

        $this->assertEquals($resultCode, 0);
        $this->assertEquals(filesize($file1), 0);
        $this->assertFileExists($file1.'.1.gz');
        $this->assertEquals(filesize($file2), 0);
        $this->assertFileExists($file2.'.1.gz');
    }

    /** @test **/
    public function it_can_rotate_file_archive()
    {
        $file1 = $this->tmpDir.'/file1';
        $file2 = $this->tmpDir.'/file2';

        $resultCode = Artisan::call('rotate:files', [
            '--file'    => [ $file1, $file2 ],
            '--dir'     => $this->tmpDir.'/archive',
        ]);

        Event::assertDispatched(RotateWasSuccessful::class, 2);

        $this->assertEquals($resultCode, 0);
        $this->assertEquals(filesize($file1), 0);
        $this->assertFileExists(dirname($file1).'/archive/'.basename($file1).'.1.gz');
        $this->assertEquals(filesize($file2), 0);
        $this->assertFileExists(dirname($file2).'/archive/'.basename($file2).'.1.gz');
    }

    /** @test **/
    public function it_can_rotate_file_max()
    {
        $file = $this->tmpDir.'/file1';

        for ($n = 0; $n < 5; $n++) {
            file_put_contents($file, 'test');

            $resultCode = Artisan::call('rotate:files', [
                '--file'        => [ $file ],
                '--max-files'    => 3,
            ]);

            $this->assertEquals($resultCode, 0);
        }

        Event::assertDispatched(RotateWasSuccessful::class, 5);

        $this->assertEquals(filesize($file), 0);

        for ($n = 1; $n < 4; $n++) {
            $this->assertFileExists($file.'.'.$n.'.gz');
        }

        $this->assertFileNotExists($file.basename($file).'.4.gz');
    }
}
