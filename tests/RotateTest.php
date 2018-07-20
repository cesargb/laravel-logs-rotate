<?php

namespace Cesargb\File\Rotate\Test;

use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Artisan;
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
}
