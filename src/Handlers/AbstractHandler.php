<?php
namespace Cesargb\File\Rotate\Handlers;

use Exception;
use Monolog\Handler\StreamHandler;
use Illuminate\Support\Facades\Log;
use Monolog\Handler\RotatingFileHandler;
use Cesargb\File\Rotate\Events\RotateHasFailed;
use Cesargb\File\Rotate\Helpers\Log as LogHelper;
use Cesargb\File\Rotate\Events\RotateIsNotNecessary;

abstract class AbstractHandler implements HandlerInterface
{
    protected $file;

    protected $compress;

    protected $dir_to_archive;

    protected $file_rotated;

    public function __construct($file, bool $compress = true, $dir_to_archive = null)
    {
        $this->file = $file;
        $this->compress = $compress;
        $this->dir_to_archive = $this->dir_to_archive ?? dirname($file);
    }

    protected function validate()
    {
        clearstatcache();

        if (! is_file($this->file)) {
            event(new RotateIsNotNecessary($this->file, 'The file '.$this->file.' nos exists'));

            return false;
        }

        if (! is_writable($this->file)) {
            event(new RotateHasFailed($this->file, new Exception('File '.$this->file.' is not writable.')));

            return false;
        }

        if (! is_dir($this->dir_to_archive)) {
            if (! file_exists($this->dir_to_archive)) {
                if (! mkdir($this->dir_to_archive, 0777, true)) {
                    event(new RotateHasFailed($this->file, new Exception('Directory '.$this->dir_to_archive.' to archive logs is not writable')));

                    return false;
                }
            } else {
                event(new RotateHasFailed($this->file, new Exception('Directory '.$this->dir_to_archive.' to archive exits and is not a directory')));

                return false;
            }
        }

        if (! is_writable($this->dir_to_archive)) {
            event(new RotateHasFailed($this->file, new Exception('Directory '.$this->dir_to_archive.' to archive logs is not writable')));

            return false;
        }

        if (filesize($this->file) == 0) {
            event(new RotateIsNotNecessary($this->file, 'The file '.$this->file.' is empty'));

            return false;
        }

        return true;
    }

    protected function close()
    {
        LogHelper::closeHandlers();
    }
}
