<?php

namespace berthott\Permissions\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * The permission model
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
     * Get permissions from an array of route names.
     *
     * @param string|string[] $array
     * @param bool $strict in strict mode the route name must match exactly. In non-strict mode the route name must be contained in the permission.
     */
    public static function get(mixed $routeNames, bool $strict = false): Collection
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
    
    /**
     * Get the routes associated with this permission.
     */
    public function routes()
    {
        return $this->hasMany(PermissionRoute::class);
    }
}
