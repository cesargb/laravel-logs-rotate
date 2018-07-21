<?php

namespace Cesargb\File\Rotate\Handlers;

use Exception;
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

        if (empty($dir_to_archive)) {
            $this->dir_to_archive = dirname($file);
        } else {
            if (substr($dir_to_archive, 0, 1) == '/') {
                $this->dir_to_archive = $dir_to_archive;
            } else {
                $this->dir_to_archive = dirname($file).'/'.$dir_to_archive;
            }
        }
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

    protected function rebaseArchiveDir($file)
    {
        return $this->dir_to_archive.'/'.basename($file);
    }

    protected function close()
    {
        LogHelper::closeHandlers();
    }

    protected function moveData($fileSource, $fileDestination)
    {
        $fdSource = fopen($fileSource, 'r+');

        if (! $fdSource) {
            return false;
        }

        if (! flock($fdSource, LOCK_EX)) {
            fclose($fdSource);

            return false;
        }

        if (! copy($fileSource, $fileDestination)) {
            fclose($fdSource);

            return false;
        }

        if (! ftruncate($fdSource, 0)) {
            fclose($fdSource);

            unlink($fileDestination);

            return false;
        }

        flock($fdSource, LOCK_UN);

        fflush($fdSource);

        fclose($fdSource);

        clearstatcache();

        return true;
    }
}
