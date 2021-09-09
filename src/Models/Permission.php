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
        'controller', 'action', 'name',
    ];

    /**
     * Get permissions from array.
     * 
     * @param mixed $array
     * @return \Illuminate\Database\Eloquent\Collection
     */
    static public function getPermissionsFromRouteActions($routeActions)
    {
        if ($routeActions === '*') return self::all();
        
        $routeActions = is_array($routeActions) ? $routeActions : [$routeActions];
        $ret = new Collection();
        foreach($routeActions as $permission) {
            if (strpos($permission, '@')) {
                $action = explode('@', $permission);
                $ret = $ret->concat(self::query()->where('controller', $action[0])
                                                 ->when(array_key_exists(1, $action), function($query) use ($action) {
                                                     return $query->where('action', $action[1]);
                                                 })->get());
            } else {
                $ret = $ret->concat(self::query()->where('name', 'like', "%{$permission}%")->get());
            }
            
        }
        return $ret;
    }
}
