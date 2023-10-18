<?php

namespace berthott\Permissions\Models\Traits;

/**
 * Trait to add the ignore permission functionality.
 */
trait IgnorePermissionRoutes
{
    /**
     * Only ignore the following actions.
     * 
     * `config('permissions.ignoreActions')` will be ignored as well.
     *  
     * **optional**
     * 
     * Defaults to `[]`.
     * 
     * @api
     */
    public static function ignoreOnly(): array
    {
        return [];
    }

    /**
     * Do not ignore the following actions.
     * 
     * Only applied if ignore only is empty.
     *  
     * **optional**
     * 
     * Defaults to `[]`.
     * 
     * @api
     */
    public static function doNotIgnore(): array
    {
        return [];
    }
}
