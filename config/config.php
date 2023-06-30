<?php

use HaydenPierce\ClassFinder\ClassFinder;

return [

    /*
    |--------------------------------------------------------------------------
    | Route Middleware Configuration
    |--------------------------------------------------------------------------
    |
    | An array of all middlewares to be applied to all of the generated routes.
    |
    */

    'middleware' => [],

    /*
    |--------------------------------------------------------------------------
    | Model Namespace Configuration
    |--------------------------------------------------------------------------
    |
    | String or array with one ore multiple namespaces that should be monitored 
    | for the configured trait.
    |
    */

    'namespace' => 'App\Models',

    /*
    |--------------------------------------------------------------------------
    | Model Namespace Search Option
    |--------------------------------------------------------------------------
    |
    | Defines the search mode for the namespaces. ClassFinder::STANDARD_MODE
    | will only find the exact matching namespace, ClassFinder::RECURSIVE_MODE
    | will find all subnamespaces.
    | 
    | Beware: ClassFinder::RECURSIVE_MODE might cause some testing issues.
    |
    */

    'namespace_mode' => ClassFinder::RECURSIVE_MODE,

    /*
    |--------------------------------------------------------------------------
    | Route Prefix
    |--------------------------------------------------------------------------
    |
    | Defines the route prefix.
    |
    */

    'prefix' => 'api',

    /*
    |--------------------------------------------------------------------------
    | Actions to ignore
    |--------------------------------------------------------------------------
    |
    | Defines an array of actions that should be ignored by default.
    |
    */

    'ignoreActions' => [],
];
