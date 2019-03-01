<?php

namespace Cesargb\File\Rotate\Commands;

use Event;
use Illuminate\Console\Command;
use Cesargb\File\Rotate\Events\RotateHasFailed;
use Cesargb\File\Rotate\Handlers\RotativeHandler;
use Cesargb\File\Rotate\Helpers\Log as LogHelper;
use Cesargb\File\Rotate\Events\RotateWasSuccessful;
use Cesargb\File\Rotate\Events\RotateIsNotNecessary;

class Rotate extends Command
{
    protected $signature = 'rotate:logs';

    protected $description = 'Rotate logs of Laravel';

    public function handle()
    {
        $maxFiles = config('rotate.log_max_files', 5);
        $compress = config('rotate.log_compress_files', true);
        $archive_dir = config('rotate.archive_dir');

        Event::listen(RotateWasSuccessful::class, function ($event) {
            $this->line("\t".'<info>Rotated</> to: '.$event->fileRotated);
        });

        Event::listen(RotateIsNotNecessary::class, function ($event) {
            $this->line("\t".'<comment>Rotation is not necessary</>: '.$event->message);
        });

        Event::listen(RotateHasFailed::class, function ($event) {
            $this->line("\t".'<error>Rotation failed</>: '.$event->exception->getMessage());
        });

        foreach (LogHelper::getLaravelLogFiles() as $file) {
            $this->line('Rotate file '.$file);

            $rotate = new RotativeHandler($file, $maxFiles, $compress, $archive_dir);

            $rotate->run();
        }
    }
}
