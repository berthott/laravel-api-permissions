# Laravel-API-Permissions - A helper for API Permissions in Laravel

Protect all your routes with a single middleware.

## Installation

```
$ composer require berthott/laravel-api-permissions
```

## Usage

* Add the `permissions` middleware to the routes you want to protect.
* Seed the permissions table by publishing
```
$ php artisan vendor:publish --provider="berthott\Permissions\ApiPermissionsServiceProvider" --tag="seeders"
```

## Options

To change the default options use
```
$ php artisan vendor:publish --provider="berthott\Permissions\ApiPermissionsServiceProvider" --tag="config"
```
* `middleware`: an array of middlewares that will be added to the generated routes
* `prefix`: route prefix. Defaults to `api`

## Compatibility

Tested with Laravel 8.x.

## License

See [License File](license.md). Copyright © 2021 Jan Bladt.