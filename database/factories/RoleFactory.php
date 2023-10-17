<?php

namespace berthott\Permissions\Database\Factories;

use berthott\Permissions\Models\Role;
use Illuminate\Database\Eloquent\Factories\Factory;
 
class RoleFactory extends Factory
{
  /**
   * The name of the factory's corresponding model.
   */
  protected $model = Role::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => fake()->name(),
        ];
    }
}