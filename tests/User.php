<?php

namespace berthott\Permissions\Tests;


use berthott\Crudable\Models\Traits\Crudable;
use berthott\Permissions\Models\Traits\HasPermissions;
use berthott\Permissions\Models\Traits\HasRoles;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Crudable, HasRoles, HasPermissions, HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
    ];
}
