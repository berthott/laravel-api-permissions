<?php

namespace berthott\Permissions\Models\Traits;

use Facades\berthott\Permissions\Helpers\PermissionsHelper;
use berthott\Permissions\Models\Role;

/**
 * Trait to add the roles functionality.
 */
trait HasRoles
{
    /**
     * Has a relating role or the user itself the permissions.
     * 
     * @param mixed $permissions one or multiple permissions to check
     * @param bool $any if multiple permissions are checked this indicates whether to look for all or one available permission.
     * 
     * @api
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
     * 
     * @api
     */
    public function roles()
    {
        return $this->belongsToMany(Role::class);
    }
}
