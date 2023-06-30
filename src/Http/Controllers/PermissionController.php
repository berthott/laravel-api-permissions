<?php

namespace berthott\Permissions\Http\Controllers;

use Facades\berthott\Permissions\Services\IgnorePermissionRoutesService;
use berthott\Permissions\Models\Permission;
use Illuminate\Routing\Controller;

class PermissionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Permission::all()->filter(function ($permission) {
            return !IgnorePermissionRoutesService::isIgnored($permission->name);
        })->map(function (Permission $permission) {
            if ($permission->routes->count() <= 1) {
                $permission->unsetRelation('routes');
            }
            return $permission;
        })->values();
    }
}
