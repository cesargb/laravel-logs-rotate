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
        $files = $this->option('file');
        $compress = $this->option('compress');
        $maxFiles = $this->option('max-files');
        $dir = $this->option('dir');

        Event::listen(RotateWasSuccessful::class, function ($event) {
            $this->line("\t".'<info>Rotated</> to: '.$event->fileRotated);
        });

        Event::listen(RotateIsNotNecessary::class, function ($event) {
            $this->line("\t".'<comment>Rotated is nos necessary</>: '.$event->message);
        });

        Event::listen(RotateHasFailed::class, function ($event) {
            $this->line("\t".'<error>Rotated failed</>: '.$event->exception->getMessage());
        });

        foreach ($files as $file) {
            $this->line('Rotate file '.$file.': ');

            $rotate = new RotativeHandler($file, $maxFiles, $compress, $dir);

            $rotate->run();
        }
    }
}
