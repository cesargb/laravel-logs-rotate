<?php

namespace Cesargb\File\Rotate\Commands;

use Cesargb\File\Rotate as RotateFile;
use Illuminate\Console\Command;

class Rotate extends Command
{
    protected $signature = 'logs:rotate';

    protected $description = 'Rotate logs';

    public function handle()
    {
        $result = RotateFile::file(
            app()->storagePath().'/logs/laravel.log',
            config('rotate.log_max_files', 7),
            config('rotate.log_compress_files', true)
        );

        if ($result) {
            $this->info('Logs was rotated');
        } else {
            $this->error('Logs rotate failed');
        }
    }
}
