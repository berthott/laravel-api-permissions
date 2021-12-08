<?php

namespace berthott\Permissions\Tests;

use berthott\Crudable\Models\Traits\Crudable;
use berthott\Permissions\Models\Traits\IgnorePermissions;
use Illuminate\Foundation\Auth\User as Authenticatable;

class IgnoreEntity extends Authenticatable
{
    use Crudable, IgnorePermissions;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
    ];
}
