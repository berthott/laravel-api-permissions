<?php

namespace berthott\Permissions\Facades;

use Illuminate\Support\Facades\Facade;

class PermissionsHelper extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'PermissionsHelper';
    }
}
