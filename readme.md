# Laravel-API-Permissions

A helper for API Permissions in Laravel. Protect all your routes with a single middleware.

## Installation

```sh
$ composer require berthott/laravel-api-permissions
```

## Usage

* **Caution:** This package assumes, that you name all your routes in the way laravel does: `models.action`.
* Add the `permissions` middleware to the routes you want to protect.
* Seed the permissions table by writing your own Seeder:
  * `php artisan make:seeder PermissionTableSeeder`
  * utilize `berthott\Permissions\Helpers\PermissionsHelper` to actually seed the permissions
    * `PermissionsHelper::resetTables()` will truncate all permission related tables.
    * `PermissionsHelper::buildRoutePermissions()` will build the permissions table. You might pass an array for mapping routes to permissions. E.g.
      ```php
      [
        '*.destroy' => [
           '*.destroy',
           '*.destroy_many'
        ],
      ]
      ```
    * `PermissionsHelper::buildUiPermissions()` will add UI permissions, that will only be handle by the frontend.
* If the `migrate` option is `true` the package will migrate five tables for you: `roles`, `role_user`, `permissions`, `permissionables` and `permission_routes`.
* If the `migrate` option is `false` and you want to write your own migration you can have look at the default migrations by running
```sh
$ php artisan vendor:publish --provider="berthott\Permissions\ApiPermissionsServiceProvider" --tag="migrations"
```
* You can add `permissions` to your `User` model by adding the `HasPermissions` trait.
* You can add `roles` to you `User` model by adding the `HasRoles` trait.
* You can use either or both of the above options.
* You may ignore specific routes actions from the permission system by adding them to the `ignoreActions` config, or by added the `IgnorePermissionRoutes`trait.

## Options

To change the default options use
```sh
$ php artisan vendor:publish --provider="berthott\Permissions\ApiPermissionsServiceProvider" --tag="config"
```
* Inherited from [laravel-targetable](https://docs.syspons-dev.com/laravel-targetable)
  * `namespace`: String or array with one ore multiple namespaces that should be monitored for the configured trait. Defaults to `App\Models`.
  * `namespace_mode`: Defines the search mode for the namespaces. `ClassFinder::STANDARD_MODE` will only find the exact matching namespace, `ClassFinder::RECURSIVE_MODE` will find all subnamespaces. Defaults to `ClassFinder::RECURSIVE_MODE`.
  * `prefix`: Defines the route prefix. Defaults to `api`.
* General Package Configuration
  * `middleware`: An array of all middlewares to be applied to all of the generated routes. Defaults to `[]`.
  * `ignoreActions`: Defines an array of actions that should be ignored by default. Defaults to `[]`.
  * `migrate`: Defines wether or not to migrate standard tables.. Defaults to `true`.

## Compatibility

Tested with Laravel 10.x.

## License

See [License File](license.md). Copyright © 2023 Jan Bladt.