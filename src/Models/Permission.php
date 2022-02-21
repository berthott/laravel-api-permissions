<?php

namespace berthott\Permissions\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

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
     */
    public static function get($routeNames, bool $strict = false): Collection
    {
        if ('*' === $routeNames) {
            return self::all();
        }

        $routeNames = is_array($routeNames) ? $routeNames : [$routeNames];
        $ret = new Collection();
        foreach ($routeNames as $permission) {
            $entries = $strict
                ? self::query()->where('name', $permission)->get()
                : self::query()->where('name', 'like', "%{$permission}%")->get();
            $ret = $ret->concat($entries);
        }

        return $ret;
    }
}
