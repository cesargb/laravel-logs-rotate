<?php

namespace Cesargb\LaravelLog\Helpers;

use Illuminate\Support\Facades\Log as LogLaravel;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Handler\StreamHandler;

class Log
{
    protected static function getHandlers()
    {
        return LogLaravel::getHandlers();
    }

    public static function getLaravelLogFiles()
    {
        $files = [];

        foreach (self::getHandlers() as $handler) {
            if ($handler instanceof StreamHandler && ! $handler instanceof RotatingFileHandler) {
                $files[] = $handler->getUrl();
            }
        }

        return $files;
    }

    public static function closeHandlers()
    {
        foreach (self::getHandlers() as $handler) {
            if ($handler instanceof StreamHandler && ! $handler instanceof RotatingFileHandler) {
                $handler->close();
            }
        }
    }
}
