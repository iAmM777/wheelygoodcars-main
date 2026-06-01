<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::factory(120)->create();

        User::factory()->count(2)->state(fn (): array => [
            'phone_number' => null,
        ])->create();

        User::factory()->count(2)->state(fn (): array => [
            'phone_number' => fake()->numerify('06########'),
        ])->create();

        User::factory()->count(2)->state(fn (): array => [
            'phone_number' => fake()->numerify('06########'),
        ])->create();

        User::factory()->count(2)->state(fn (): array => [
            'phone_number' => fake()->numerify('06########'),
        ])->create();

        User::factory()->count(2)->state(fn (): array => [
            'phone_number' => fake()->numerify('06########'),
        ])->create();

        User::factory()->count(2)->state(fn (): array => [
            'phone_number' => fake()->numerify('06########'),
        ])->create();
    }
}
