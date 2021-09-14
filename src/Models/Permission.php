<?php

namespace berthott\Permissions\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;

/**
 * @mixin IdeHelperPermission
 */
class Permission extends Model
{
    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
    ];

    /**
     * Get permissions from array.
     * 
     * @param string|string[] $array
     * @return \Illuminate\Database\Eloquent\Collection
     */
    static public function get($routeNames)
    {
        if ($routeNames === '*') return self::all();
        
        $routeNames = is_array($routeNames) ? $routeNames : [$routeNames];
        $ret = new Collection();
        foreach($routeNames as $permission) {
            $ret = $ret->concat(self::query()->where('name', 'like', "%{$permission}%")->get());
        }
        return $ret;
    }
}
