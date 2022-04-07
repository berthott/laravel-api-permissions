<?php

namespace berthott\Permissions\Models\Traits;

trait IgnorePermissionRoutes
{
    /**
     * Only ignore the following actions
     */
    public static function ignoreOnly(): array
    {
        return [];
    }
}
