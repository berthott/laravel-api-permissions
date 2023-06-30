<?php

namespace berthott\Permissions\Models;

use Exception;
use Illuminate\Database\Eloquent\Model;

/**
 * A permission route maps a permission to a route.
 * 
 * This is necessary for route mapping.
 */
class PermissionRoute extends Model
{
    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'route', 'permission_id',
    ];

    /**
     * Get an instance from the routes name.
     */
    public static function fromRoute(string $route): PermissionRoute
    {
        return self::query()->where('route', $route)->first();
    }

    /**
     * Get the permission name form a mapping. 
     * 
     * The mapping should look like this:
     * ```php
     * [
     *     '*.mapped' => [
     *         '*.routeAction1',
     *         '*.create',
     *     ],
     * ];
     * ```
     */
    public static function getMappedPermissionName(string $route, array $mapping = null): string
    {
        if (str_starts_with($route, '*')) {
            throw new Exception('Expected a valid route');
        }

        if (!$mapping) {
            return $route;
        }
        foreach ($mapping as $permission => $routes) {
            if (in_array($route, $routes)) {
                if (str_starts_with($permission, '*')) {
                    throw new Exception('Permission wildcards only for routes with wildcards.');
                }
                return $permission;
            } else {
                $splitRoute = explode('.', $route);
                foreach ($routes as $mappedRoute) {
                    $splitMappedRoute = explode('.', $mappedRoute);
                    if ($splitMappedRoute[1] === $splitRoute[1]) {
                        if (str_contains($permission, '.')) {
                            $splitPermission = explode('.', $permission);
                            return $splitRoute[0].'.'.$splitPermission[1];
                        } else {
                            return $permission;
                        }
                    }
                }
            }
        }
        return $route;
    }

    /**
     * Get the associated permission.
     */
    public function permission()
    {
        return $this->belongsTo(Permission::class);
    }
}
