<?php

namespace Cesargb\File\Rotate\Test;

use Cesargb\File\Rotate\RotateServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

abstract class TestCase extends Orchestra
{
    /**
     * @param \Illuminate\Foundation\Application $app
     *
     * @return array
     */
    protected function getPackageProviders($app)
    {
        return [
            RotateServiceProvider::class,
        ];
    }
}
