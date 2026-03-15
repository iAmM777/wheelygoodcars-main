<?php

namespace Database\Seeders;

use App\Models\Car;
use App\Models\User;
use Illuminate\Database\Seeder;

class CarSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $userIds = User::query()->pluck('id')->all();

        if (empty($userIds)) {
            return;
        }

        Car::factory(20)
            ->state(fn () => [
                'user_id' => fake()->randomElement($userIds),
            ])
            ->create();
    }
}
