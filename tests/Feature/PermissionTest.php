<?php

namespace berthott\Permissions\Tests\Feature;

use berthott\Permissions\Models\Permission;
use Illuminate\Support\Facades\Route;
use berthott\Permissions\Tests\TestCase;
use Illuminate\Support\Str;

class PermissionTest extends TestCase
{
    public function test_the_test_setup(): void
    {
        $this->assertDatabaseHas('permissions', ['name' => 'users.index']);
        $user = $this->createUserWithPermissions(Permission::where('name', 'like', 'users%')->get()->pluck('name')->toArray());
        $role = $user->roles->first();
        $this->assertModelExists($user);
        $this->assertModelExists($role);
        $this->assertDatabaseHas('permissionables', [
            'permissionable_id' => $role->id,
            'permission_id' => Permission::where('name', 'users.index')->first()->id
        ]);
    }

    public function test_all_permissions_successfully()
    {
        $permissions = Permission::where('name', 'like', 'users%')->get()->pluck('name')->toArray();
        $user = $this->createUserWithPermissions($permissions);
        foreach($permissions as $permission) {
            $route = Route::getRoutes()->getByName($permission);
            foreach($route->methods() as $method) {
                $this->actingAs($user)->json($method, route($permission, ['user' => $user->id, 'name' => Str::random(5)]))->assertSuccessful();
            }
        };
    }

    public function test_all_permissions_fail()
    {
        $permissions = Permission::where('name', 'like', 'users%')->get()->pluck('name')->toArray();
        $user = $this->createUserWithPermissions();
        foreach($permissions as $permission) {
            $route = Route::getRoutes()->getByName($permission);
            foreach($route->methods() as $method) {
                $this->actingAs($user)->json($method, route($permission, ['user' => $user->id, 'name' => Str::random(5)]))->assertForbidden();
            }
        };
    }
}
