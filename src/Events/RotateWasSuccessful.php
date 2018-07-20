<?php

namespace Cesargb\File\Rotate\Events;

class RotateWasSuccessful
{
    public $fileSource;

    public $fileRotated;

    public function __construct($fileSource, $fileRotated)
    {
        $this->fileSource = $fileSource;

        $this->fileRotated = $fileRotated;
    }
}
