<?php

namespace Cesargb\LaravelLog\Events;

use Exception;

class RotateHasFailed
{
    public string $filename;

    public Exception $exception;

    public function __construct(string $filename, Exception $exception)
    {
        $this->filename = $filename;

        $this->exception = $exception;
    }
}
