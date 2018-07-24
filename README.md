[![Build Status](https://travis-ci.org/cesargb/laravel-logs-rotate.svg?branch=master)](https://travis-ci.org/cesargb/laravel-logs-rotate)
[![StyleCI](https://styleci.io/repos/119604039/shield?branch=master)](https://styleci.io/repos/119604039)
[![Latest Stable Version](https://img.shields.io/packagist/v/cesargb/laravel-logs-rotate.svg)](https://packagist.org/packages/cesargb/laravel-logs-rotate)

# Rotate Laravel logs files and compress

This package allows you to rotate the Laravel record file with compression. This method is useful if you use logger channel `single` (StreamHandler)

## Instalation

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
php artisan vendor:publish --provider="Cesargb\File\Rotate\RotateServiceProvider" --tag=config
```
This is the contents of the published config/rotate.php config file:

```php
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
    | Archive Dir
    |--------------------------------------------------------------------------
    |
    | This value determine the folder where save the files after rotated.
    | Leave null to archive in the same folder of yours logs.
    |
    */
    'archive_dir'   => null,

    /*
    |--------------------------------------------------------------------------
    | Schedule Rotate
    |--------------------------------------------------------------------------
    |
    | Determine when must be run the cron.
    | You can disable the schedule change the option enable at false.
    | You can change the frecuency with option cron.
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

`Cesargb\File\Rotate\Events\RotateWasSuccessful`

This event will be fired when rotated was successful.

It has two public properties:

* fileSource: the full path of file to rotate
* fileRotated: the full path of file rotated

### RotateIsNotNecessary

`Cesargb\File\Rotate\Events\RotateIsNotNecessary`

If file log not exists or is empty this event will be fired.

It has two public properties:

* fileSource: the full path of file to rotate
* message: Descriptive message of the reason

### RotateHasFailed

`Cesargb\File\Rotate\Handlers\RotativeHandler`

This event will be fired when something goes wrong while rotated.

It has two public properties:

* fileSource: the full path of file to rotate
* exception: an object that extends PHP's Exception class.

## About

You can only rotate the logs file was generate with logger channel StreamHandler.

## Test

Run test with:

```bash
composer test
```

## Knowledge Issue

* [#8](https://github.com/cesargb/laravel-logs-rotate/issues/8) While the file is being rotated, any record of another process may be lost.

## Contributing

All contributions are welcome.

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
