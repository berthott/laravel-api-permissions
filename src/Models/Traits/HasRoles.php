<?php

namespace berthott\Permissions\Models\Traits;

use berthott\Permissions\Facades\PermissionsHelper;
use berthott\Permissions\Models\Role;

trait HasRoles
{
    /**
     * Has a relating role or the user itself the permission.
     */
    public function hasRoleOrDirectPermissions(mixed $permissions, bool $any = true): bool
    {
        foreach ($this->roles as $role) {
            $hasPermission = $role->hasPermissions($permissions, $any);
            if ($hasPermission) {
                return true;
            }
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
