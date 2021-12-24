<?php

namespace Cesargb\LaravelLog\Handlers;

use Cesargb\LaravelLog\Events\RotateHasFailed;
use Cesargb\LaravelLog\Helpers\Log as LogHelper;
use Exception;

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

        return $this->validateFile() && $this->validateDirectory();
    }

    private function validateFile(): bool
    {
        if (! is_file($this->file)) {
            return false;
        }

        if (filesize($this->file) == 0) {
            return false;
        }

        if (! is_writable($this->file)) {
            return false;
        }

        return true;
    }

    private function validateDirectory(): bool
    {
        if (is_dir($this->dir_to_archive)) {
            if (! is_writable($this->dir_to_archive)) {
                event(new RotateHasFailed($this->file, new Exception('Directory '.$this->dir_to_archive.' to archive logs is not writable')));

                return false;
            }

            return true;
        }

        if (file_exists($this->dir_to_archive)) {
            event(new RotateHasFailed($this->file, new Exception('Directory '.$this->dir_to_archive.' to archive exists and is not a directory')));

            return false;
        }

        if (! mkdir($this->dir_to_archive, 0777, true)) {
            event(new RotateHasFailed($this->file, new Exception('Directory '.$this->dir_to_archive.' to archive logs is not writable')));

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

        if ($fdSource === false) {
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
