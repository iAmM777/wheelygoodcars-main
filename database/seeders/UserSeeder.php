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
        // Create a hardcoded admin user
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => \Illuminate\Support\Facades\Hash::make('password'),
            'phone_number' => null,
            'admin' => true,
            'email_verified_at' => now(),
        ]);

        // Create regular users (non-admin)
        User::factory(120)->create(['admin' => false]);

        User::factory()->count(2)->state(fn (): array => [
            'phone_number' => null,
            'admin' => false,
        ])->create();

        User::factory()->count(2)->state(fn (): array => [
            'phone_number' => fake()->numerify('06########'),
            'admin' => false,
        ])->create();

        User::factory()->count(2)->state(fn (): array => [
            'phone_number' => fake()->numerify('06########'),
            'admin' => false,
        ])->create();

        User::factory()->count(2)->state(fn (): array => [
            'phone_number' => fake()->numerify('06########'),
            'admin' => false,
        ])->create();

        User::factory()->count(2)->state(fn (): array => [
            'phone_number' => fake()->numerify('06########'),
            'admin' => false,
        ])->create();

        User::factory()->count(2)->state(fn (): array => [
            'phone_number' => fake()->numerify('06########'),
        ])->create();
    }
}
