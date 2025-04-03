# Rotate Laravel logs files and compress

![tests](https://github.com/cesargb/laravel-logs-rotate/workflows/tests/badge.svg)
[![style](https://github.com/cesargb/laravel-logs-rotate/actions/workflows/style-fix.yml/badge.svg)](https://github.com/cesargb/laravel-logs-rotate/actions/workflows/style-fix.yml)
[![phpstan](https://github.com/cesargb/laravel-logs-rotate/actions/workflows/phpstan.yml/badge.svg)](https://github.com/cesargb/laravel-logs-rotate/actions/workflows/phpstan.yml)
[![Latest Stable Version](https://img.shields.io/packagist/v/cesargb/laravel-logs-rotate.svg)](https://packagist.org/packages/cesargb/laravel-logs-rotate)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/cesargb/laravel-logs-rotate/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/cesargb/laravel-logs-rotate/?branch=master)

This package allows you to rotate the Laravel record file with compression. This method is useful if you use logger channel `single` (StreamHandler)

## Installation

This package can be used in Laravel 5.5 or higher.

You can install the package via composer:

```bash
composer require cesargb/laravel-logs-rotate
```

## Usage

At this moment, every day at 00:00 your application executes a schedule to rotate the Laravel record files.

## Configuration

If you need to change the frequency or another function, you can modify the config file.

You can publish config file with:

```bash
php artisan vendor:publish --provider="Cesargb\LaravelLog\RotateServiceProvider" --tag=config
```

This is the contents of the published config/rotate.php config file:

```php
<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Compression Enable
    |--------------------------------------------------------------------------
    |
    | This option defines if the file rotated must be compressed.
    | If you prefer not compress file, set this value at false.
    */
    'log_compress_files' => true,

    /*
    |--------------------------------------------------------------------------
    | Schedule Rotate
    |--------------------------------------------------------------------------
    |
    | Determine when must be run the cron.
    | You can disable the schedule change the option enable at false.
    | You can change the frequency with option cron.
    |
    */
    'schedule' => [
        'enable'    => true,
        'cron'      => '0 0 * * *',
    ],

    /*
    |--------------------------------------------------------------------------
    | Max Files Rotated
    |--------------------------------------------------------------------------
    |
    | This value determine the max number of files rotated in the archive folder.
    |
    */
    'log_max_files' => env('LOG_MAX_FILES', 5),

    /*
    |--------------------------------------------------------------------------
    | Truncate Log file
    |--------------------------------------------------------------------------
    |
    | This option defines if the log file must be truncated after rotated.
    | If you prefer not truncate file, set this value at false.
    */
    'truncate' => env('LOG_TRUNCATE', true),

    /*
    |--------------------------------------------------------------------------
    | Other files to rotated
    |--------------------------------------------------------------------------
    |
    | Array the other foreing files
    |
    | Example:
    |   'foreign_files' => [
            storage_path('/logs/worker.log')
    |   ]
    |
    */
    'foreign_files' => []
];
```

## Command

You have a command to rotate other files, `rotate:files`

```bash
php artisan rotate:files --help
Description:
  Rotate files

Usage:
  rotate:files [options]

Options:
  -f, --file[=FILE]            Files to rotate (multiple values allowed)
  -c, --compress[=COMPRESS]    Compress the file rotated [default: "true"]
  -m, --max-files[=MAX-FILES]  Max files rotated [default: "5"]
  -d, --dir[=DIR]              Dir where archive the file rotated
```

## Events

Every time a file is rotated one of these events occurs:

### RotateWasSuccessful

`Cesargb\LaravelLog\Events\RotateWasSuccessful`

This event will be fired when rotated was successful.

It has two public properties:

* filename: the full path of file to rotate
* filenameTarget: the full path of file rotated

### RotateHasFailed

`Cesargb\LaravelLog\Events\RotativeHandler`

This event will be fired when an error occurs while rotated

It has two public properties:

* filename: the full path of file to rotate
* exception: an object that extends PHP's Exception class.

## About

You can only rotate the logs file was generate with logger channel StreamHandler.

## Test

Run test with:

```bash
composer test
```

## Upgrading

Please see [UPGRADING](UPGRADING.md) for details.

## Contributing

Any contributions are welcome.

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
