<?php

namespace Database\Seeders;

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
        foreach (Route::getRoutes() as $route) {
            if (!in_array('permissions', $route->action['middleware'])) {
                continue;
            }
            $name = explode('@', $route->getActionName());
            Permission::create([
                'controller' => $name[0],
                'action' => $name[1],
                'name' => $route->getName()
            ]);
        }
    }
}
