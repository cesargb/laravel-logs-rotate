<?php

namespace Cesargb\File\Rotate\Commands;

use Event;
use Illuminate\Console\Command;
use Cesargb\File\Rotate\Events\RotateHasFailed;
use Cesargb\File\Rotate\Handlers\RotativeHandler;
use Cesargb\File\Rotate\Events\RotateWasSuccessful;
use Cesargb\File\Rotate\Events\RotateIsNotNecessary;

class RotateFile extends Command
{
    protected $signature = 'rotate:files
                                        {--f|file=* : Files to rotate}
                                        {--c|compress=true : Compress the file rotated}
                                        {--m|max-files=5 : Max files rotated}
                                        {--d|dir= : Dir where archive the file rotated}';

    protected $description = 'Rotate files';

    public function handle()
    {
        foreach ($this->option('file') as $file) {
            $this->line('Rotate file '.$file.': ');

            $rotate = new RotativeHandler(
                $file,
                $this->option('max-files'),
                $this->option('compress'),
                $this->option('dir')
            );

            if ($rotate->run()) {
                $this->line("\t".'<info>Rotated</>');
            } else {
                $this->line("\t".'<comment>Not rotated</>');
            }
        }
    }
}
