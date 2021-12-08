<?php

namespace berthott\Permissions\Tests;


use berthott\Crudable\Models\Traits\Crudable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Entity extends Authenticatable
{
    use Crudable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
    ];
}
