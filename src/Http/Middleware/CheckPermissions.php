<?php

namespace berthott\Permissions\Http\Middleware;

use Facades\berthott\Permissions\Services\IgnorePermissionRoutesService;
use Facades\berthott\Permissions\Helpers\PermissionsHelper;
use berthott\Permissions\Models\PermissionRoute;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/**
 * Middleware to handle the check the permissions.
 */
class CheckPermissions
{
    /**
     * Handle an incoming request.
     * 
     * If the route is not ignored, check for direct and role permissions.
     */
    public function handle(Request $request, Closure $next): mixed
    {
        $user = Auth::user();
        $routeName = Route::currentRouteName();
        $ignorePermissions = IgnorePermissionRoutesService::isIgnored($routeName);
        if (!$ignorePermissions) {
            $permission = PermissionRoute::fromRoute($routeName)->permission->name;
            $hasDirectPermissions = PermissionsHelper::hasTrait($user, 'berthott\Permissions\Models\Traits\HasPermissions');
            $hasRolePermissions = PermissionsHelper::hasTrait($user, 'berthott\Permissions\Models\Traits\HasRoles');
        }
        if ($ignorePermissions ||
            $hasRolePermissions && $user->hasRoleOrDirectPermissions($permission) ||
            $hasDirectPermissions && $user->hasPermissions($permission)) {
            return $next($request);
        }

        return response()->json(['error' => 'Unauthorized.'], 403);
    }
}
