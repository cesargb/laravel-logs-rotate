<?php

namespace Cesargb\LaravelLog\Events;

class RotateWasSuccessful
{
    public string $filename;

    public string $filenameTarget;

    public function __construct(string $filename, string $filenameTarget)
    {
        $this->filename = $filename;

        $this->filenameTarget = $filenameTarget;
    }
}
