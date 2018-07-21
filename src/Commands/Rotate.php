<?php

namespace Cesargb\File\Rotate\Commands;

use Illuminate\Console\Command;
use Cesargb\File\Rotate\Handlers\RotativeHandler;
use Cesargb\File\Rotate\Helpers\Log as LogHelper;

class Rotate extends Command
{
    protected $signature = 'rotate:logs';

    protected $description = 'Rotate logs of Laravel';

    public function handle()
    {
        $maxFiles = config('rotate.log_max_files', 5);
        $compress = config('rotate.log_compress_files', true);
        $archive_dir = config('rotate.archive_dir');

        foreach (LogHelper::getLaravelLogFiles() as $file) {
            $this->output->write('Rotate file '.$file.': ');

            $rotate = new RotativeHandler($file, $maxFiles, $compress, $archive_dir);

            if ($rotate->run()) {
                $this->info('ok');
            } else {
                $this->error('err');
            }
        }
    }
}
