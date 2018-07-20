[![Build Status](https://travis-ci.org/cesargb/laravel-logs-rotate.svg?branch=master)](https://travis-ci.org/cesargb/laravel-logs-rotate)
[![StyleCI](https://styleci.io/repos/119604039/shield?branch=master)](https://styleci.io/repos/119604039)

# Rotate files log with compression

This package permit rotate logs in Laravel with compression.

## Instalation

This package can be used in Laravel 5.5 or higher.

You can install the package via composer:

```bash
composer require cesargb/laravel-logs-rotate
```

You can publish config file with:

```
php artisan vendor:publish --provider="Cesargb\File\Rotate\RotateServiceProvider" --tag=config
```
This is the contents of the published config/rotate.php config file:

```php
return [
    'log_max_files' => config('app.log_max_files'),

    'log_compress_files' => true,

    'logs_rotate_schedule' => '0 0 * * *',
];
```


## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
