<?php

namespace berthott\Permissions\Models\Traits;

use berthott\Permissions\Models\Permission;

/**
 * Trait to add the permissions functionality.
 */
trait HasPermissions
{
    /**
     * Add one or multiple permissions to this model.
     * 
     * @param mixed $permissions one or multiple permissions to add
     * @param mixed $except one or multiple permissions that should be excluded from the $permissions array.
     * @param mixed $action you can choose between the laravel functions 'attach' (default), 'sync' and 'syncWithoutDetach'.
     * 
     * @api
     */
    public function addPermissions(mixed $permissions, mixed $except = [], string $action = 'attach'): void
    {
        if (!$permissions) {
            return;
        }
        $permissionModels = Permission::get($permissions);
        $exceptModels = Permission::get($except);
        $models = $permissionModels->filter(function ($permission) use ($exceptModels) {
            return !$exceptModels->find($permission->id);
        });
        $this->permissions()->$action($models);
    }

    /**
     * Has this model the given permissions.
     * 
     * @param mixed $permissions one or multiple permissions to check
     * @param bool $any if multiple permissions are checked this indicates whether to look for all or one available permission.
     * 
     * @api
     */
    public function hasPermissions(mixed $permissions, bool $any = true)
    {
        $foundAny = false;
        $foundAll = true;
        $permissionModels = Permission::get($permissions, true);
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
     * 
     * @api
     */
    public function permissions()
    {
        return $this->morphToMany(Permission::class, 'permissionable');
    }
}
