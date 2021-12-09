<?php

namespace berthott\Permissions\Models;

use berthott\Crudable\Models\Traits\Crudable;
use berthott\Permissions\Models\Traits\HasPermissions;
use Illuminate\Database\Eloquent\Model;

/**
 * @mixin IdeHelperRole
 */
class Role extends Model
{
    use Crudable;
    use HasPermissions;

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
}
