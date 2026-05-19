<?php

namespace Database\Seeders;

use App\Models\Car;
use App\Models\Tag;
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
        $tagIds = Tag::query()->pluck('id')->all();

        if (empty($userIds) || empty($tagIds)) {
            return;
        }

        Car::factory(250)
            ->state(fn () => [
                'user_id' => fake()->randomElement($userIds),
            ])
            ->create()
            ->each(function (Car $car) use ($tagIds) {
                // Attach 1-4 random tags to each car
                $randomTags = fake()->randomElements($tagIds, fake()->numberBetween(1, 4));
                $car->tags()->attach($randomTags);
            });
    }
}
