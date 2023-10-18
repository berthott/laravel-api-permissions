<?php

namespace berthott\Permissions\Tests\Feature;

use berthott\Permissions\Models\Permission;
use berthott\Permissions\Tests\DoNotIgnoreEntity;
use berthott\Permissions\Tests\Entity;
use berthott\Permissions\Tests\IgnoreEntity;
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

    public function test_all_user_permissions_successfully()
    {
        $permissions = Permission::where('name', 'like', 'users%')->get()->pluck('name')->toArray();
        $user = $this->createUserWithPermissions($permissions);
        foreach ($permissions as $permission) {
            $route = Route::getRoutes()->getByName($permission);
            if (in_array(explode('.', $route->getName())[1], ['destroy_many'])) {
                continue;
            }
            foreach ($route->methods() as $method) {
                $this->actingAs($user)->json($method, route($permission, ['user' => $user->id, 'name' => Str::random(5)]))->assertSuccessful();
            }
        };
    }

    public function test_all_user_permissions_fail()
    {
        $permissions = Permission::where('name', 'like', 'users%')->get()->pluck('name')->toArray();
        $user = $this->createUserWithPermissions();
        foreach ($permissions as $permission) {
            $route = Route::getRoutes()->getByName($permission);
            if (in_array(explode('.', $route->getName())[1], config('permissions.ignoreActions'))) {
                continue;
            }
            foreach ($route->methods() as $method) {
                $this->actingAs($user)->json($method, route($permission, ['user' => $user->id, 'name' => Str::random(5)]))->assertForbidden();
            }
        };
    }

    public function test_all_entity_permissions_fail()
    {
        $permissions = Permission::where('name', 'like', 'entities%')->get()->pluck('name')->toArray();
        $user = $this->createUserWithPermissions();
        $entity = Entity::create(['name' => 'Test']);
        foreach ($permissions as $permission) {
            $route = Route::getRoutes()->getByName($permission);
            foreach ($route->methods() as $method) {
                $response = $this->actingAs($user)->json($method, route($permission, ['entity' => $entity->id, 'name' => Str::random(5)]));
                if (in_array(explode('.', $route->getName())[1], array_merge(config('permissions.ignoreActions')))) {
                    $response->assertSuccessful();
                } else {
                    $response->assertForbidden();
                }
            }
        };
    }

    public function test_all_ignore_entities_permissions_succeed()
    {
        $permissions = Permission::where('name', 'like', 'ignore_entities%')->get()->pluck('name')->toArray();
        $user = $this->createUserWithPermissions();
        $ignore_entity = IgnoreEntity::create(['name' => 'Test']);
        foreach ($permissions as $permission) {
            $route = Route::getRoutes()->getByName($permission);
            foreach ($route->methods() as $method) {
                $response = $this->actingAs($user)->json($method, route($permission, ['ignore_entity' => $ignore_entity->id, 'name' => Str::random(5)]));
                if (in_array(explode('.', $route->getName())[1], array_merge(config('permissions.ignoreActions'), IgnoreEntity::ignoreOnly()))) {
                    $response->assertSuccessful();
                } else {
                    $response->assertForbidden();
                }
            }
        };
    }

    public function test_all_do_not_ignore_entities_permissions_succeed()
    {
        $permissions = Permission::where('name', 'like', 'do_not_ignore_entities%')->get()->pluck('name')->toArray();
        $user = $this->createUserWithPermissions();
        $do_not_ignore_entity = DoNotIgnoreEntity::create(['name' => 'Test']);
        foreach ($permissions as $permission) {
            $route = Route::getRoutes()->getByName($permission);
            foreach ($route->methods() as $method) {
                $response = $this->actingAs($user)->json($method, route($permission, ['do_not_ignore_entity' => $do_not_ignore_entity->id, 'name' => Str::random(5)]));
                if (!in_array(explode('.', $route->getName())[1], DoNotIgnoreEntity::doNotIgnore())) {
                    $response->assertSuccessful();
                } else {
                    $response->assertForbidden();
                }
            }
        };
    }

    public function test_permissions_route()
    {
        $this->get(route('permissions.index'))
            ->assertStatus(200)
            ->assertJsonFragment(['name' => 'users.index'])
            ->assertJsonFragment(['name' => 'entities.index'])
            ->assertJsonFragment(['name' => 'do_not_ignore_entities.index'])
            ->assertJsonMissing(['name' => 'ignore_entities.index'])
            ->assertJsonMissing(['name' => 'do_not_ignore_entities.show'])
            ->assertJsonMissing(['name' => 'users.schema']);
    }

    public function test_permissions_seeder()
    {
        $this->assertDatabaseHas('permissions', ['name' => 'entities.index']);
        $this->assertDatabaseHas('permissions', ['name' => 'do_not_ignore_entities.index']);
        $this->assertDatabaseMissing('permissions', ['name' => 'ignore_entities.index']);
        $this->assertDatabaseMissing('permissions', ['name' => 'do_not_ignore_entities.show']);
    }

    public function test_strict_permissions_too_much()
    {
        $entity = Entity::create(['name' => 'Test']);
        $deleteUser = $this->createUserWithPermissions('entities.destroy', ['entities.destroy_many']);
        $this->actingAs($deleteUser)->delete(route('entities.destroy_many'), ['ids' => [$entity->id]])->assertStatus(403);
    }

    public function test_strict_permissions_too_few()
    {
        $entity = Entity::create(['name' => 'Test']);
        $deleteManyUser = $this->createUserWithPermissions('entities.destroy_many');
        $this->actingAs($deleteManyUser)->delete(route('entities.destroy', ['entity' => $entity->id]))->assertStatus(403);
    }
}
