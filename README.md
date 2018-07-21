[![Build Status](https://travis-ci.org/cesargb/laravel-logs-rotate.svg?branch=master)](https://travis-ci.org/cesargb/laravel-logs-rotate)
[![StyleCI](https://styleci.io/repos/119604039/shield?branch=master)](https://styleci.io/repos/119604039)
[![Latest Stable Version](https://img.shields.io/packagist/v/cesargb/laravel-logs-rotate.svg)](https://packagist.org/packages/cesargb/laravel-logs-rotate)

# Rotate files log with compression

This package permit rotate logs file of Laravel with compression.

## Instalation

This package can be used in Laravel 5.5 or higher.

You can install the package via composer:

```bash
composer require cesargb/laravel-logs-rotate
```

## Usage

In this moment, every days at 00:00 your App run a schedule to rotate the Laravel logs files.

## Custom Usage

If need change the frecuency or other function you can modify the config file.

You can publish config file with:

```
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

## About

You only can rotate the logs file was generate with logger channel StreamHandler.

## Test

Run test with:

```bash
composer test
```

## Contributing

All contributing are wellcome

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
