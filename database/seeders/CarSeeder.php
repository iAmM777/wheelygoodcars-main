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
        $tagsByName = Tag::query()->pluck('id', 'name');
        $tagIds = $tagsByName->values()->all();
        $lowMileageTagId = $tagsByName->get('Lage kilometerstand');

        if (empty($userIds) || empty($tagIds)) {
            return;
        }

        Car::factory(250)
            ->state(fn () => [
                'user_id' => fake()->randomElement($userIds),
            ])
            ->create()
            ->each(function (Car $car) use ($tagIds, $lowMileageTagId) {
                $availableTagIds = $tagIds;

                if ($lowMileageTagId !== null) {
                    $availableTagIds = array_values(array_filter(
                        $availableTagIds,
                        fn (int $tagId): bool => $tagId !== $lowMileageTagId
                    ));
                }

                // Attach 1-4 random tags to each car, excluding the low-mileage tag from the random pool.
                $randomTags = fake()->randomElements($availableTagIds, fake()->numberBetween(1, min(4, count($availableTagIds))));

                if ($lowMileageTagId !== null && (int) $car->mileage < 20000) {
                    $randomTags[] = $lowMileageTagId;
                }

                $randomTags = array_values(array_unique($randomTags));
                $car->tags()->attach($randomTags);
            });
    }
}
