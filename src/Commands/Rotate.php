<?php

namespace Cesargb\LaravelLog\Commands;

use Cesargb\LaravelLog\Helpers\Log as LogHelper;
use Cesargb\LaravelLog\Rotate as LaravelLogRotate;
use Illuminate\Console\Command;

class Rotate extends Command
{
    protected $signature = 'rotate:logs';

    protected $description = 'Rotate logs of Laravel';

    public function handle()
    {
        $this->rotateLaravelLogs();

        $this->rotareForeingFiles();
    }

    protected function rotateLaravelLogs()
    {
        foreach (LogHelper::getLaravelLogFiles() as $file) {
            $this->line('Rotate file: '.$file);

            $this->rotateFile($file);
        }
    }

    protected function rotareForeingFiles()
    {
        foreach (config('rotate.foreing_files', []) as $file) {
            $this->line('Rotate file: '.$file);

            $this->rotateFile($file);
        }
    }

    protected function rotateFile(string $filename)
    {
        // $rotate = new RotativeHandler(
        //     $file,
        //     config('rotate.log_max_files', 5),
        //     config('rotate.log_compress_files', true),
        //     config('rotate.archive_dir')
        // );

        // if ($rotate->run()) {
        //     $this->line("\t".'<info>Rotated</>');
        // } else {
        //     $this->line("\t".'<comment>Not rotated</>');
        // }

        $rotation = new LaravelLogRotate();

        $rotation->file($filename);
    }
}
