<?php

namespace berthott\Permissions\Http\Middleware;

use berthott\Permissions\Facades\IgnorePermissions;
use berthott\Permissions\Facades\PermissionsHelper;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

class CheckPermissions
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): mixed
    {
        $user = Auth::user();
        $routeName = Route::currentRouteName();
        $hasDirectPermissions = PermissionsHelper::hasTrait($user, 'berthott\Permissions\Models\Traits\HasPermissions');
        $hasRolePermissions = PermissionsHelper::hasTrait($user, 'berthott\Permissions\Models\Traits\HasRoles');
        $ignorePermissions = false;
        if ($requestedModel = IgnorePermissions::getTarget()) {
            $ignorePermissions = PermissionsHelper::hasTrait($requestedModel, 'berthott\Permissions\Models\Traits\IgnorePermissions');
        }
        if ($ignorePermissions ||
            $hasRolePermissions && $user->hasRoleOrDirectPermissions($routeName) ||
            $hasDirectPermissions && $user->hasPermissions($routeName)) {
            return $next($request);
        }

        return response()->json(['error' => 'Unauthorized.'], 403);
    }
}
