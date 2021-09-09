<?php

namespace berthott\Permissions;

use berthott\Permissions\Http\Controllers\PermissionController;
use berthott\Permissions\Http\Middleware\CheckPermissions;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class ApiPermissionsServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        // add config
        $this->mergeConfigFrom(__DIR__.'/../config/config.php', 'permissions');
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        // publish config
        $this->publishes([
            __DIR__.'/../config/config.php' => config_path('permissions.php'),
        ], 'config');

        // publish seed
        $this->publishes([
            __DIR__ . '/../database/seeders/PermissionTableSeeder.php' => database_path('seeders/PermissionTableSeeder.php'),
        ], 'seeders');

        // publish migrations
        /* $this->publishes([
            __DIR__ . '/../database/migrations/PermissionTableSeeder.php' => database_path('migrations/PermissionTableSeeder.php'),
        ], 'migrations'); */

        // load migrations
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');

        // add routes
        Route::group($this->routeConfiguration(), function () {
            Route::get('permissions', [PermissionController::class, 'index'])->name('permissions.index');
        });

        // add Middleware
        $router = $this->app->make(Router::class);
        $router->aliasMiddleware('permissions', CheckPermissions::class);
    }
    
    protected function routeConfiguration()
    {
        return [
            'middleware' => config('permissions.middleware'),
            'prefix' => config('permissions.prefix')
        ];
    }
}
