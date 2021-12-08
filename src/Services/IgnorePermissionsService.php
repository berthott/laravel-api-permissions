<?php

namespace berthott\Permissions\Services;

use HaydenPierce\ClassFinder\ClassFinder;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Route;

class IgnorePermissionsService
{
    /**
     * Collection with all classes.
     */
    private Collection $classes;

    /**
     * The Constructor.
     */
    public function __construct()
    {
        $this->initClasses();
    }

    /**
     * Get the classes collection.
     */
    public function getClasses(): Collection
    {
        return $this->classes;
    }

    /**
     * Initialize the classes collection.
     */
    private function initClasses(): void
    {
        $classes = [];
        $namespaces = config('permissions.namespace');
        foreach (is_array($namespaces) ? $namespaces : [$namespaces] as $namespace) {
            foreach (ClassFinder::getClassesInNamespace($namespace) as $class) {
                foreach (class_uses_recursive($class) as $trait) {
                    if ('berthott\Permissions\Models\Traits\IgnorePermissions' == $trait) {
                        array_push($classes, $class);
                    }
                }
            }
        }
        $this->classes = collect($classes);
    }

    /**
     * Get the table ignored?
     */
    public function isIgnored(string $routeName): bool
    {
        if ($this->classes->isEmpty()) {
            return false;
        }

        $routeArray = explode('.', $routeName);

        if (in_array($routeArray[1], config('permissions.ignoreActions'))) {
            return true;
        }

        $model = Str::studly(Str::singular($routeArray[0]));
        return $this->classes->first(function ($class) use ($model) {
            return Arr::last(explode('\\', $class)) === $model;
        }) ?: false;
    }
}
