<?php

namespace Cesargb\LaravelLog\Test\Commands;

use Cesargb\LaravelLog\Events\RotateWasSuccessful;
use Cesargb\LaravelLog\Test\TestCase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Event;

class RotateFileTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        file_put_contents($this->tmpDir . '/file1', 'test');
        file_put_contents($this->tmpDir . '/file2', 'test');
    }

    public function test_it_can_rotate_file()
    {
        $file1 = $this->tmpDir . '/file1';
        $file2 = $this->tmpDir . '/file2';

        $resultCode = Artisan::call('rotate:files', [
            '--file' => [$file1, $file2],
        ]);

        Event::assertDispatched(RotateWasSuccessful::class, 2);

        $this->assertEquals($resultCode, 0);
        $this->assertFileExists($file1 . '.1.gz');
        $this->assertFileExists($file2 . '.1.gz');
    }
}
