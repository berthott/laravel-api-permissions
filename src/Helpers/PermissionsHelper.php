<?php

namespace berthott\Permissions\Helpers;

use Facades\berthott\Permissions\Services\IgnorePermissionRoutesService;
use berthott\Permissions\Models\Permission;
use berthott\Permissions\Models\PermissionRoute;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;

/**
 * Helper for seeding the permissions table.
 * 
 * @api
 */
class PermissionsHelper
{
    /**
     * Find weather the class uses a trait.
     */
    public static function hasTrait(string|object $class, string $trait): bool
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
     * 
     * @api
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
     * 
     * Permissions will be added for all routes that use the `permissions` middleware.
     * Ignored routes won't be added.
     * 
     * You may map specific route actions into a single permission adding a `$mapping` parameter.
     * For example the following array would map all destroy and destroy_many route actions into
     * a single destroy permission.
     * 
     * ```php
     * [
     *  '*.destroy' => [
     *      '*.destroy',
     *      '*.destroy_many'
     *   ],
     * ]
     * ```
     * 
     * @api
     */
    public static function buildRoutePermissions(array $mapping = null)
    {
        foreach (Route::getRoutes() as $route) {
            if (!in_array('permissions', $route->action['middleware']) ||
                IgnorePermissionRoutesService::isIgnored($route->getName())) {
                continue;
            }
            $permission = self::getOrCreatePermission($route->getName(), $mapping);
            PermissionRoute::create([
                'route' => $route->getName(),
                'permission_id' => $permission->id,
            ]);
        }
    }
    
    /**
     * Get or create a permission
     */
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
     * 
     * UI permissions don't actually guard any backend route, but are added to the
     * permissions table in order to be represented in the frontend. They can be
     * checked by the frontend.
     * 
     * @api
     */
    public static function buildUiPermissions(array $routes)
    {
        foreach ($routes as $route) {
            Permission::create(['name' => $route]);
        }
    }
}
