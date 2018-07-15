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
	    $logs = glob(app()->storagePath().'/logs/*.log');

    	foreach ($logs as $logfile){
		    $result = RotateFile::file(
			    $logfile,
			    config('rotate.log_max_files', 7),
			    config('rotate.log_compress_files', true)
		    );
		    if ($result) {
			    $this->info('Log '.$logfile.' was rotated');
		    } else {
			    $this->error('Log '.$logfile.' rotate failed');
		    }
	    }
    }
}
