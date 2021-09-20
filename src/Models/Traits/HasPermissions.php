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
     *
     * @return void
     */
    public function addPermissions($permissions, $except = []): void
    {
        if (!$permissions) {
            return;
        }
        $permissionModels = Permission::get($permissions);
        $exceptModels = Permission::get($except);
        $this->permissions()->attach($permissionModels->filter(function ($permission) use ($exceptModels) {
            return !$exceptModels->find($permission->id);
        }));
    }

    /**
     * Has this role the given permissions.
     *
     * @param mixed $permissions
     * @param bool  $any
     *
     * @return void
     */
    public function hasPermissions($permissions, $any = true)
    {
        $foundAny = false;
        $foundAll = true;
        $permissionModels = Permission::get($permissions);
        foreach ($permissionModels as $instance) {
            $foundAny = $this->permissions->contains($instance);
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
