<?php

namespace Cesargb\LaravelLog;

use Cesargb\LaravelLog\Events\RotateHasFailed;
use Cesargb\LaravelLog\Events\RotateWasSuccessful;
use Cesargb\Log\Rotation;

class Rotate
{
    public function files(array $filenames)
    {
        array_walk($filenames, function ($filename) {
            $this->file($filename);
        });
    }

    public function file(string $filename, array $options = []): bool
    {
        return $this->buildRotateDefault($options)->rotate($filename);
    }

    private function buildRotateDefault(array $options = []): Rotation
    {
        return new Rotation(array_merge(
            [
                'files' => config('rotate.log_max_files', 366),
                'compress' => config('rotate.log_compress_files', true),
                'then' => function ($filenameRotated, $filename) {
                    event(new RotateWasSuccessful($filename, $filenameRotated));
                },
                'catch' => function ($error) {
                    event(new RotateHasFailed('', $error));
                },
            ],
           $options
        ));
    }
}
