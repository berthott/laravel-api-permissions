<?php

namespace berthott\Permissions\Services;

use berthott\Permissions\Models\Traits\IgnorePermissionRoutes;
use berthott\Targetable\Services\TargetableService;
use Illuminate\Support\Str;
use Illuminate\Support\Arr;


/**
 * TargetableService implementation for an sxable class.
 * 
 * @link https://docs.syspons-dev.com/laravel-targetable
 */
class IgnorePermissionRoutesService extends TargetableService
{
    public function __construct()
    {
        parent::__construct(IgnorePermissionRoutes::class, 'permissions');
    }

    /**
     * Should the given route be ignored from the permissions.
     */
    public function isIgnored(string $routeName): bool
    {
        $routeArray = explode('.', $routeName);
        $action = array_key_exists(1, $routeArray) ? $routeArray[1] : null;
        if (in_array($action, config('permissions.ignoreActions'))) {
            return true;
        }

        if ($this->targetables->isEmpty()) {
            return false;
        }

        $model = Str::studly(Str::singular($routeArray[0]));
        if ($modelWithNamespace = $this->targetables->first(fn ($class) => Arr::last(explode('\\', $class)) === $model)) {
            if (count($modelWithNamespace::ignoreOnly())) {
                return in_array($action, $modelWithNamespace::ignoreOnly());
            }
            if (count($modelWithNamespace::doNotIgnore())) {
                return !in_array($action, $modelWithNamespace::doNotIgnore());
            }
            return true;
        }
        return false;
    }
}
