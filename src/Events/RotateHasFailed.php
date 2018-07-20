<?php
namespace Cesargb\File\Rotate\Events;

use Exception;

class RotateHasFailed
{
    public $fileSource;

    public $exception;

    public function __construct($fileSource, Exception $exception)
    {
        $this->fileSource = $fileSource;

        $this->exception = $exception;
    }
}
