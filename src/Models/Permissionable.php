<?php

namespace berthott\Permissions\Models;

use berthott\ApiCache\Models\Traits\FlushesApiCache;
use Illuminate\Database\Eloquent\Model;

class Permissionable extends Model
{
    use FlushesApiCache;

    /**
     * Returns an array of dependencies to flush.
     */
    public static function cacheDependencies(): array
    {
        return ['roles'];
    }
}
