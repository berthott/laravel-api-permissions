<?php

namespace berthott\Permissions\Models;

use berthott\Crudable\Models\Traits\Crudable;
use berthott\ApiCache\Models\Traits\FlushesApiCache;
use berthott\Permissions\Database\Factories\RoleFactory;
use berthott\Permissions\Models\Traits\HasPermissions;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Model;

/**
 * The role moddel
 */
class Role extends Model
{
    use Crudable;
    use FlushesApiCache;
    use HasPermissions;
    use HasFactory;

    public static function attachables(): array
    {
        return [
            'permissions'
        ];
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'title', 'description',
    ];

    /**
     * The relations to eager load on every query.
     *
     * @var array
     */
    protected $with = [
        'permissions',
    ];

    /**
     * Create a new factory instance for the model.
     */
    protected static function newFactory(): Factory
    {
        return RoleFactory::new();
    }
}
