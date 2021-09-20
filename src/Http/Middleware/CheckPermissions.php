<?php

namespace berthott\Permissions\Http\Middleware;

use berthott\Permissions\Facades\PermissionsHelper;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

class CheckPermissions
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): mixed
    {
        $user = Auth::user();
        $hasDirectPermissions = PermissionsHelper::hasTrait($user, 'berthott\Permissions\Models\Traits\HasPermissions');
        $hasRolePermissions = PermissionsHelper::hasTrait($user, 'berthott\Permissions\Models\Traits\HasRoles');
        if ($hasRolePermissions && $user->hasRoleOrDirectPermissions(Route::currentRouteName()) ||
            $hasDirectPermissions && $user->hasPermissions(Route::currentRouteName())) {
            return $next($request);
        }

        return response()->json(['error' => 'Unauthorized.'], 403);
    }
}
