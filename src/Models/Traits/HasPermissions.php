<?php

namespace berthott\Permissions\Models\Traits;

use berthott\Permissions\Models\Permission;

trait HasPermissions
{
    /**
     * Add permissions to this model.
     * 
     * @param mixed $permissions
     * @param mixed $except
     * @return void
     */
    public function addPermissions($permissions, $except = [])
    {
        if (!$permissions) {
            return;
        }
        $permissionModels = Permission::getPermissionsFromRouteActions($permissions);
        $exceptModels = Permission::getPermissionsFromRouteActions($except);
        $this->permissions()->attach($permissionModels->filter(function($permission) use ($exceptModels) {
            return !$exceptModels->find($permission->id);
        }));
    }

    /**
     * Has this role the given permissions.
     * 
     * @param mixed $permissions
     * @param bool  $any
     * @return void
     */
    public function hasPermissions($permissions, $any = true)
    {
        $foundAny = false;
        $foundAll = true;
        $permissionModels = Permission::getPermissionsFromRouteActions($permissions);
        foreach($permissionModels as $instance) {
            $foundAny = $this->permissions->contains($instance) || 
                isset($this->roles) ? $this->permissions->contains($instance) : false;
            if (!$foundAny) {
                $foundAll = false;
            }
        }
        return $any ? $foundAny : $foundAll;
    }

    /**
     * The permissions that belong to the model.
     */
    public function permissions()
    {
        return $this->morphToMany(Permission::class, 'permissionable');
    }
}