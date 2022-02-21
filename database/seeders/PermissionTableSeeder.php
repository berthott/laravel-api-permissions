<?php

namespace Database\Seeders;

use berthott\Permissions\Facades\IgnorePermissions;
use berthott\Permissions\Models\Permission;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Route;

class PermissionTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $routes = Route::getRoutes();
        foreach ($routes as $route) {
            if (!in_array('permissions', $route->action['middleware']) ||
                IgnorePermissions::isIgnored($route->getName())) {
                continue;
            }
            Permission::create([
                'name' => $route->getName()
            ]);
        }
    }
}
