<?php

namespace berthott\Permissions\Facades;

use Illuminate\Support\Facades\Facade;

class IgnorePermissions extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'IgnorePermissions';
    }
}
