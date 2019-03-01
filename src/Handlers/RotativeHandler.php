<?php

namespace Cesargb\File\Rotate\Handlers;

use Cesargb\File\Rotate\Events\RotateHasFailed;
use Cesargb\File\Rotate\Events\RotateWasSuccessful;

class RotativeHandler extends AbstractHandler
{
    const EXTENSION_COMPRESS = 'gz';

    protected $max_files;

    public function __construct($file, $max_files = 0, bool $compress = true, $dir_to_archive = null)
    {
        parent::__construct($file, $compress, $dir_to_archive);

        $this->max_files = $max_files;
    }

    public function run()
    {
        if (! $this->validate()) {
            return false;
        }

        $this->file_rotated = $this->rebaseArchiveDir($this->getRotatedFileName());

        if ($this->rotate()) {
            $this->close();

            event(new RotateWasSuccessful($this->file, $this->file_rotated));

            return true;
        } else {
            event(new RotateHasFailed($this->file, new Exception('Failed to move file '.$this->file.' to '.$this->file_rotated)));

            return false;
        }
    }

    protected function rotate()
    {
        if ($this->compress) {
            $file_tmp_name = tempnam(dirname($this->file), 'laravel_log_rotate');

            if ($this->moveData($this->file, $file_tmp_name)) {
                $fd_tmp = fopen($file_tmp_name, 'r');

                if ($fd_tmp) {
                    $fd_compress = gzopen($this->file_rotated, 'w');

                    while (! feof($fd_tmp)) {
                        gzwrite($fd_compress, fread($fd_tmp, 1024 * 512));
                    }

                    gzclose($fd_compress);
                    fclose($fd_tmp);

                    unlink($file_tmp_name);

                    return true;
                } else {
                    return false;
                }
            } else {
                return false;
            }
        } else {
            if ($this->moveData($this->file, $this->file_rotated)) {
                return true;
            } else {
                return false;
            }
        }
    }

    protected function getRotatedFileName()
    {
        $fileInfo = pathinfo($this->file);

        $glob = $fileInfo['dirname'].'/'.$fileInfo['filename'];

        if (! empty($fileInfo['extension'])) {
            $glob .= '.'.$fileInfo['extension'];
        }

        $glob .= '.*';

        if ($this->compress) {
            $glob .= '.'.self::EXTENSION_COMPRESS;
        }

        $curFiles = glob($glob);

        for ($n = count($curFiles); $n > 0; $n--) {
            $file_to_move = str_replace('*', $n, $glob);

            if (file_exists($file_to_move)) {
                if ($this->max_files > 0 && $n >= $this->max_files) {
                    unlink($file_to_move);
                } else {
                    rename($file_to_move, str_replace('*', $n + 1, $glob));
                }
            }
        }

        return str_replace('*', '1', $glob);
    }
}
