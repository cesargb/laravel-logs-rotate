

# Create link for authenticate in Laravel without password

This package permit create a link for authenticate without user or password.

## Instalation

This package can be used in Laravel 5.4 or higher.

You can install the package via composer:

```bash
composer require cesargb/laravel-logs-rotate
```

If you have Laravel 5.4, you must add the service provider in config/app.php file:

```php
'providers' => [
    // ...
    Cesargb\Files\Rotate\RotateServiceProvider::class,
];
```

You can publish config file with:

```
php artisan vendor:publish --provider="Cesargb\File\Rotate\RotateServiceProvider" --tag=config
```
This is the contents of the published config/magiclink.php config file:

```php
return [
    'log_max_files' => config('app.log_max_files'),

    'log_compress_files' => true,

    'logs_rotate_schedule' => '0 0 * * *',
];
```


## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
