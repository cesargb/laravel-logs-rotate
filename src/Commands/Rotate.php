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
        if (config('app.log') == 'single') {
            $result = RotateFile::file(
                app()->storagePath().'/logs/laravel.log',
                config('rotate.log_max_files', config('app.log_max_files')),
                config('rotate.log_compress_files', true)
            );

            if ($result) {
                $this->info('Logs was rotated');
            } else {
                $this->error('Logs rotate failed');
            }
        } else {
            $this->error('Laravel must be configure with single log. You can change this in app/config.php');
        }
   }
}
