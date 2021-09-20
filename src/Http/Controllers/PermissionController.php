<?php

namespace berthott\Permissions\Http\Controllers;

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
        return Permission::all();
    }
}
