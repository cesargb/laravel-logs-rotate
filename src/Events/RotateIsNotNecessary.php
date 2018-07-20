<?php
namespace Cesargb\File\Rotate\Events;


class RotateIsNotNecessary
{
    public $fileSource;

    public $message;

    public function __construct($fileSource, $message)
    {
        $this->fileSource = $fileSource;

        $this->message = $message;
    }
}
