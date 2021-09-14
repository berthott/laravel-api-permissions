<?php

namespace berthott\Permissions\Models\Traits;

use berthott\Permissions\Models\Role;
use berthott\Permissions\Facades\PermissionsHelper;

trait HasRoles
{
    /**
     * Has a relating role or the user itself the permission.
     * 
     * @param mixed $permissions
     * @param bool  $any
     * @return void
     */
    public function hasRoleOrDirectPermissions($permissions, $any = true)
    {
        foreach ($this->roles as $role) {
            $hasPermission = $role->hasPermissions($permissions, $any);
            if ($hasPermission) return true;
        }
        return PermissionsHelper::hasTrait($this, 'berthott\Permissions\Models\Traits\HasPermissions') 
            ? $this->hasPermissions($permissions, $any) 
            : false;
    }

    /**
     * The roles that belong to the model.
     */
    public function roles()
    {
        return $this->belongsToMany(Role::class);
    }
}