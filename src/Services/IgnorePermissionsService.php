<?php

namespace berthott\Permissions\Services;

use HaydenPierce\ClassFinder\ClassFinder;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Illuminate\Support\Arr;

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
     * Get the target model.
     */
    public function getTarget(): string
    {
        if (!request()->segments() || $this->classes->isEmpty()) {
            return '';
        }
        $model = Str::studly(Str::singular(request()->segment(count(explode('/', config('permissions.prefix'))) + 1)));

        return $this->classes->first(function ($class) use ($model) {
            return Arr::last(explode('\\', $class)) === $model;
        }) ?: '';
    }
}
