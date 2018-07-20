<?php

namespace Cesargb\File\Rotate\Helpers;

use App;
use Monolog\Handler\StreamHandler;
use Monolog\Handler\RotatingFileHandler;
use Illuminate\Support\Facades\Log as LogLaravel;

class Log
{
    public static function laravelVersion()
    {
        $va = explode('.', App::version(), 3);

        return $va[0].'.'.$va[1] ?? '0';
    }

    protected static function getHandlers()
    {
        if (self::laravelVersion() == '5.5') {
            return LogLaravel::getMonolog()->getHandlers();
        } else {
            return LogLaravel::driver()->getHandlers();
        }
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
                if (method_exists($handler, 'close')) {
                    $handler->close();
                }
            }
        }
    }
}
