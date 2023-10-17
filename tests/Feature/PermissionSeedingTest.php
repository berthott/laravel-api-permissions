<?php

namespace berthott\Permissions\Tests\Feature;

use Facades\berthott\Permissions\Helpers\PermissionsHelper;
use berthott\Permissions\Models\Permission;
use berthott\Permissions\Models\PermissionRoute;
use berthott\Permissions\Tests\TestCase;
use Exception;
use Illuminate\Support\Facades\Route;

const MAPPING = [
    'newName' => ['users.index'],
    'anotherName' => [
        '*.store',
        '*.show'
    ],
    '*.destroy' => [
        '*.destroy',
        '*.destroy_many'
    ],
];

class PermissionSeedingTest extends TestCase
{
    public function test_route_exist(): void
    {
        $expectedRoutes = [
            'permissions.index',
        ];
        $registeredRoutes = array_keys(Route::getRoutes()->getRoutesByName());
        foreach ($expectedRoutes as $route) {
            $this->assertContains($route, $registeredRoutes);
        }
    }

    public function test_getMappedPermissionName_valid_route(): void
    {
        $this->expectException(Exception::class);
        PermissionRoute::getMappedPermissionName('*.wrong');
    }

    public function test_permission_index(): void
    {
        PermissionsHelper::resetTables();
        PermissionsHelper::buildRoutePermissions(MAPPING);
        $response = collect($this->get(route('permissions.index'))
            ->assertSuccessful()
            ->assertJsonFragment(['name' => 'newName'])
            ->assertJsonFragment(['name' => 'anotherName'])
            ->assertJsonFragment(['name' => 'users.destroy'])
            ->assertJsonMissing(['name' => 'users.destroy_many'])->json());
        $p = $response->where('name', 'users.destroy')->first();
        $r = collect($p['routes'])->whereIn('route', ['users.destroy_many', 'users.destroy'])->count();
        $this->assertEquals($r, 2);
    }

    public function test_getMappedPermissionName_no_mapping(): void
    {
        $this->assertEquals('something.simple', PermissionRoute::getMappedPermissionName('something.simple'));
    }

    public function test_getMappedPermissionName_exact(): void
    {
        $this->assertEquals('newName', PermissionRoute::getMappedPermissionName('users.index', MAPPING));
    }

    public function test_getMappedPermissionName_wildcard_to_fixed(): void
    {
        $this->assertEquals('anotherName', PermissionRoute::getMappedPermissionName('users.store', MAPPING));
        $this->assertEquals('anotherName', PermissionRoute::getMappedPermissionName('users.show', MAPPING));
    }

    public function test_getMappedPermissionName_wildcard_to_wildcard(): void
    {
        $this->assertEquals('users.destroy', PermissionRoute::getMappedPermissionName('users.destroy', MAPPING));
        $this->assertEquals('users.destroy', PermissionRoute::getMappedPermissionName('users.destroy_many', MAPPING));
    }

    public function test_database(): void
    {
        PermissionsHelper::resetTables();
        PermissionsHelper::buildRoutePermissions(MAPPING);
        $user = $this->createUserWithPermissions('newName');
        $newName = Permission::where('name', 'newName')->first();
        $this->assertDatabaseHas('permissions', [
            'id' => $newName->id,
            'name' => 'newName',
        ]);
        $this->assertDatabaseHas('permission_routes', [
            'route' => 'users.index',
            'permission_id' => $newName->id,
        ]);
        $anotherName = Permission::where('name', 'anotherName')->first();
        $this->assertDatabaseHas('permissions', [
            'id' => $anotherName->id,
            'name' => 'anotherName',
        ]);
        $this->assertDatabaseHas('permission_routes', [
            'route' => 'users.store',
            'permission_id' => $anotherName->id,
        ]);
        $this->assertDatabaseHas('permission_routes', [
            'route' => 'users.show',
            'permission_id' => $anotherName->id,
        ]);
        $destroy = Permission::where('name', 'users.destroy')->first();
        $this->assertDatabaseHas('permissions', [
            'id' => $destroy->id,
            'name' => 'users.destroy',
        ]);
        $this->assertDatabaseHas('permission_routes', [
            'route' => 'users.destroy',
            'permission_id' => $destroy->id,
        ]);
        $this->assertDatabaseHas('permission_routes', [
            'route' => 'users.destroy_many',
            'permission_id' => $destroy->id,
        ]);
    }

    public function test_mapped_permission_success(): void
    {
        PermissionsHelper::resetTables();
        PermissionsHelper::buildRoutePermissions(MAPPING);
        $user = $this->createUserWithPermissions('newName');
        $this->actingAs($user)->get(route('users.index'))->assertSuccessful();
    }

    public function test_mapped_permission_forbidden(): void
    {
        PermissionsHelper::resetTables();
        PermissionsHelper::buildRoutePermissions(MAPPING);
        $user = $this->createUserWithPermissions('newName');
        $this->actingAs($user)->get(route('users.show', ['user' => $user->id]))->assertForbidden();
    }

    public function test_sync(): void
    {
        PermissionsHelper::resetTables();
        PermissionsHelper::buildRoutePermissions(MAPPING);
        $user = $this->createUserWithPermissions('newName');
        $this->actingAs($user)->get(route('users.index'))->assertSuccessful();
        $this->actingAs($user)->get(route('users.show', ['user' => $user->id]))->assertForbidden();
        $user->roles->first()->addPermissions('anotherName', except: [], action: 'sync');
        $user->load('roles');
        $this->actingAs($user)->get(route('users.index'))->assertForbidden();
        $this->actingAs($user)->get(route('users.show', ['user' => $user->id]))->assertSuccessful();
    }

    public function test_syncWithoutDetaching(): void
    {
        PermissionsHelper::resetTables();
        PermissionsHelper::buildRoutePermissions(MAPPING);
        $user = $this->createUserWithPermissions('newName');
        $this->actingAs($user)->get(route('users.index'))->assertSuccessful();
        $this->actingAs($user)->get(route('users.show', ['user' => $user->id]))->assertForbidden();
        $user->roles->first()->addPermissions('anotherName', except: [], action: 'syncWithoutDetaching');
        $user->load('roles');
        $this->actingAs($user)->get(route('users.index'))->assertSuccessful();
        $this->actingAs($user)->get(route('users.show', ['user' => $user->id]))->assertSuccessful();
    }
}
