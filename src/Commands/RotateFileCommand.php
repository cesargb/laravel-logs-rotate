<?php

namespace Cesargb\LaravelLog\Commands;

use Cesargb\LaravelLog\Rotate;
use Illuminate\Console\Command;

class RotateFileCommand extends Command
{
    protected $signature = 'rotate:files
                                        {--f|file=* : Files to rotate}
                                        {--c|compress=true : Compress the file rotated}
                                        {--m|max-files=5 : Max files rotated}
                                        {--d|dir= : Dir where archive the file rotated}';

    protected $description = 'Rotate files';

    public function handle()
    {
        foreach ($this->option('file') as $filename) {
            $this->line('Rotate file '.basename($filename).': ');

            $rotate = new Rotate();

            $rotate->file($filename, [
                'files' => config('rotate.log_max_files', 366),
                'compress' => config('rotate.log_compress_files', true),
                // 'then' => function () {
                //     $this->line('<info>ok</>');
                // },
                // 'catch' => function ($error) {
                //     $this->error('<comment>failed: '.$error->getMessage().'</>');
                // },
            ]);
        }
    }
}
