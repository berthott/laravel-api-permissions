<?php

namespace berthott\Permissions\Helpers;

use berthott\Permissions\Facades\IgnorePermissionRoutes;
use berthott\Permissions\Models\Permission;
use berthott\Permissions\Models\PermissionRoute;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;

class PermissionsHelper
{
    /**
     * Find weather the class uses a trait.
     *
     * @param string|object $class
     */
    public static function hasTrait($class, string $trait): bool
    {
        foreach (class_uses_recursive($class) as $t) {
            if ($t == $trait) {
                return true;
            }
        }

        return false;
    }

    /**
     * Reset permissions and permission_routes Table
     */
    public static function resetTables()
    {
        Schema::disableForeignKeyConstraints();
        Permission::query()->delete();
        Permission::truncate();
        PermissionRoute::query()->delete();
        PermissionRoute::truncate();
        Schema::enableForeignKeyConstraints();
    }

    /**
     * Build permissions for all routes.
     */
    public static function buildRoutePermissions(array $mapping = null)
    {
        foreach (Route::getRoutes() as $route) {
            if (!in_array('permissions', $route->action['middleware']) ||
                IgnorePermissionRoutes::isIgnored($route->getName())) {
                continue;
            }
            $permission = self::getOrCreatePermission($route->getName(), $mapping);
            PermissionRoute::create([
                'route' => $route->getName(),
                'permission_id' => $permission->id,
            ]);
        }
    }
    
    private static function getOrCreatePermission(string $route, array $mapping = null): Permission
    {
        $permissionName = PermissionRoute::getMappedPermissionName($route, $mapping);
        if ($permission = Permission::where('name', $permissionName)->first()) {
            return $permission;
        }
        return Permission::create([
            'name' => $permissionName,
        ]);
    }

    /**
     * Build UI permissions.
     */
    public static function buildUiPermissions(array $routes)
    {
        foreach ($routes as $route) {
            Permission::create(['name' => $route]);
        }
    }
}
