<?php

namespace berthott\Permissions\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use berthott\Permissions\Facades\PermissionsHelper;

class CheckPermissions
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
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
