<?php

namespace berthott\Permissions\Tests;

use berthott\Crudable\CrudableServiceProvider;
use berthott\Permissions\ApiPermissionsServiceProvider;
use berthott\Permissions\Models\Role;
use Database\Seeders\PermissionTableSeeder;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Schema;
use Orchestra\Testbench\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase {

  public function setUp(): void 
  {
    parent::setUp();
    $this->seed(PermissionTableSeeder::class);
  }

  protected function getPackageProviders($app)
  {
    return [
      ApiPermissionsServiceProvider::class,
      CrudableServiceProvider::class
    ];
  }

  protected function getEnvironmentSetUp($app)
  {
    $this->setUpUserTable();
    $this->setUpMigrationTables();
    Config::set('crudable.namespace', 'berthott\Permissions\Tests');
    Config::set('crudable.middleware', ['permissions']);
  }

  private function setUpUserTable(): void 
  {
    Schema::create('users', function (Blueprint $table) {
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
  }

  public static function createUserWithPermissions($permissions = ''): User
  {
      $user = User::create(['name' => 'Test']);
      $role = Role::create(['title' => 'Test']);
      $role->addPermissions($permissions);
      $user->roles()->attach($role);
      $user->load('roles');
      return $user;
  }
}