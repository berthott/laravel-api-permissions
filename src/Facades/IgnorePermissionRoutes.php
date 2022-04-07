<?php

namespace berthott\Permissions\Facades;

use Illuminate\Support\Facades\Facade;

class IgnorePermissionRoutes extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'IgnorePermissionRoutes';
    }
}
