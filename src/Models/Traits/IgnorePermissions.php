<?php

namespace berthott\Permissions\Models\Traits;

trait IgnorePermissions
{
    /**
     * Only ignore the following actions
     */
    public static function ignoreOnly(): array
    {
        return [];
    }
}
