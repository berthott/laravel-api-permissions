<?php

namespace berthott\Permissions\Helpers;

class PermissionsHelper
{
    /**
     * Find weather the class uses a trait.
     *
     * @param string|object $class
     */
    public static function hasTrait($class, string $trait): bool
    {
        foreach (class_uses_recursive($class) as $t) {
            if ($t == $trait) {
                return true;
            }
        }

        return false;
    }
}
