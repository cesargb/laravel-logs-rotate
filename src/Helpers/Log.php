<?php
namespace Cesargb\File\Rotate\Helpers;

use Monolog\Handler\StreamHandler;
use Illuminate\Support\Facades\Log as LogLaravel;
use Monolog\Handler\RotatingFileHandler;

class Log
{
    public static function getLaravelLogFiles()
    {
        $files = [];

        foreach(LogLaravel::driver()->getHandlers() as $handler) {
            if ($handler instanceof StreamHandler || $handler instanceof RotatingFileHandler) {
                $files[] = $handler->getUrl();
            }
        }

        return $files;
    }

    public static function closeHandlers()
    {
        foreach(LogLaravel::driver()->getHandlers() as $handler) {
            if ($handler instanceof StreamHandler || $handler instanceof RotatingFileHandler) {
                if (method_exists($handler, 'close')) {
                    $handler->close();
                }
            }
        }
    }
}
