<?php

namespace berthott\Permissions\Tests;

use berthott\Crudable\Models\Traits\Crudable;
use berthott\Permissions\Models\Traits\IgnorePermissionRoutes;
use Illuminate\Foundation\Auth\User as Authenticatable;

class IgnoreEntity extends Authenticatable
{
    use Crudable, IgnorePermissionRoutes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
    ];

    /**
     * Only ignore the following actions
     */
    public static function ignoreOnly(): array
    {
        return ['index'];
    }
}
