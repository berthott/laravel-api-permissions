<?php

namespace berthott\Permissions\Tests;

use berthott\ApiCache\ApiCacheServiceProvider;
use berthott\Crudable\CrudableServiceProvider;
use berthott\Permissions\ApiPermissionsServiceProvider;
use Facades\berthott\Permissions\Helpers\PermissionsHelper;
use berthott\Permissions\Models\Role;
use berthott\Scopeable\ScopeableServiceProvider;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Schema;
use Orchestra\Testbench\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    public function setUp(): void
    {
        parent::setUp();
        PermissionsHelper::buildRoutePermissions();
    }

    protected function getPackageProviders($app)
    {
        return [
          ApiPermissionsServiceProvider::class,
          ApiCacheServiceProvider::class,
          CrudableServiceProvider::class,
          ScopeableServiceProvider::class,
      ];
    }

    protected function getEnvironmentSetUp($app)
    {
        $this->setUpUserTable();
        $this->setUpMigrationTables();
        Config::set('crudable.namespace', __NAMESPACE__);
        Config::set('crudable.middleware', ['permissions']);
        Config::set('permissions.namespace', __NAMESPACE__);
        Config::set('permissions.ignoreActions', ['schema']);
    }

    private function setUpUserTable(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->timestamps();
        });
        Schema::create('entities', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->timestamps();
        });
        Schema::create('ignore_entities', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->timestamps();
        });
    }

    private function setUpMigrationTables(): void
    {
        foreach (glob(__DIR__.'/../database/migrations/*.php') as $filename) {
            include_once $filename;
        }
        (new \CreateRolesTable)->up();
        (new \CreateRoleUserTable)->up();
        (new \CreatePermissionsTable)->up();
        (new \CreatePermissionablesTable)->up();
        (new \CreatePermissionRoutesTable)->up();
    }

    public static function createUserWithPermissions($permissions = '', $except = []): User
    {
        $user = User::create(['name' => 'Test']);
        $role = Role::create(['title' => 'Test']);
        $role->addPermissions($permissions, $except);
        $user->roles()->attach($role);
        $user->load('roles');
        return $user;
    }
}
