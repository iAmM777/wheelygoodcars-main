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
        $users = User::query()->orderBy('id')->get();
        $tagIds = Tag::query()->pluck('id')->all();

        if ($users->isEmpty() || $tagIds === []) {
            return;
        }

        $specialUsers = $users->slice(-12)->values();
        $normalUsers = $users->slice(0, max(0, $users->count() - $specialUsers->count()))->values();

        $this->seedNormalCars($normalUsers, $tagIds);
        $this->seedNoPhoneUsers($specialUsers->slice(0, 2), $tagIds);
        $this->seedOldLowMileageUsers($specialUsers->slice(2, 2), $tagIds);
        $this->seedFastSellUsers($specialUsers->slice(4, 2), $tagIds);
        $this->seedLowPriceUsers($specialUsers->slice(6, 2), $tagIds);
        $this->seedNoTagUsers($specialUsers->slice(8, 2));
        $this->seedDormantUsers($specialUsers->slice(10, 2), $tagIds);
    }

    private function seedNormalCars($users, array $tagIds): void
    {
        foreach (range(1, 180) as $index) {
            $car = Car::factory()->create([
                'user_id' => $users->random()->id,
                'price' => fake()->randomFloat(2, 1500, 9500),
                'mileage' => fake()->numberBetween(15000, 220000),
                'production_year' => fake()->numberBetween(2006, (int) now()->year),
            ]);

            $createdAt = now()->subDays(fake()->numberBetween(0, 395))->subHours(fake()->numberBetween(0, 23));
            $soldAt = fake()->boolean(28) ? (clone $createdAt)->addDays(fake()->numberBetween(1, 30)) : null;

            Car::query()->whereKey($car->id)->update([
                'created_at' => $createdAt,
                'updated_at' => $soldAt ?? $createdAt,
                'sold_at' => $soldAt,
            ]);

            $tags = fake()->boolean(15) ? [] : $this->randomTags($tagIds, 1, 4);

            if ($tags !== []) {
                $car->tags()->attach($tags);
            }
        }
    }

    private function seedNoPhoneUsers($users, array $tagIds): void
    {
        foreach ($users as $user) {
            foreach (range(1, 2) as $index) {
                $this->createSeededCar($user, [
                    'price' => fake()->randomFloat(2, 2500, 8500),
                    'mileage' => fake()->numberBetween(25000, 180000),
                    'production_year' => fake()->numberBetween(2010, (int) now()->year),
                ], $this->randomTags($tagIds, 1, 3), now()->subDays(fake()->numberBetween(0, 120)));
            }
        }
    }

    private function seedOldLowMileageUsers($users, array $tagIds): void
    {
        foreach ($users as $user) {
            foreach (range(1, 2) as $index) {
                $this->createSeededCar($user, [
                    'price' => fake()->randomFloat(2, 4500, 12500),
                    'mileage' => fake()->numberBetween(6000, 38000),
                    'production_year' => fake()->numberBetween(2001, 2011),
                ], $this->randomTags($tagIds, 1, 3), now()->subDays(fake()->numberBetween(2, 90)));
            }
        }
    }

    private function seedFastSellUsers($users, array $tagIds): void
    {
        foreach ($users as $user) {
            $createdAt = now()->subDays(fake()->numberBetween(1, 30))->startOfDay();

            foreach (range(1, 4) as $index) {
                $this->createSeededCar($user, [
                    'price' => fake()->randomFloat(2, 10500, 22000),
                    'mileage' => fake()->numberBetween(12000, 160000),
                    'production_year' => fake()->numberBetween(2012, (int) now()->year),
                ], $this->randomTags($tagIds, 1, 4), $createdAt, (clone $createdAt)->addHours(fake()->numberBetween(2, 20)));
            }
        }
    }

    private function seedLowPriceUsers($users, array $tagIds): void
    {
        foreach ($users as $user) {
            foreach (range(1, 3) as $index) {
                $this->createSeededCar($user, [
                    'price' => fake()->randomFloat(2, 250, 950),
                    'mileage' => fake()->numberBetween(40000, 240000),
                    'production_year' => fake()->numberBetween(2008, (int) now()->year),
                ], $this->randomTags($tagIds, 1, 3), now()->subDays(fake()->numberBetween(0, 180)));
            }
        }
    }

    private function seedNoTagUsers($users): void
    {
        foreach ($users as $user) {
            foreach (range(1, 3) as $index) {
                $this->createSeededCar($user, [
                    'price' => fake()->randomFloat(2, 3200, 14500),
                    'mileage' => fake()->numberBetween(12000, 160000),
                    'production_year' => fake()->numberBetween(2011, (int) now()->year),
                ], [], now()->subDays(fake()->numberBetween(0, 120)));
            }
        }
    }

    private function seedDormantUsers($users, array $tagIds): void
    {
        foreach ($users as $user) {
            foreach (range(1, 2) as $index) {
                $createdAt = now()->subMonths(fake()->numberBetween(14, 18))->subDays(fake()->numberBetween(0, 18));

                $this->createSeededCar($user, [
                    'price' => fake()->randomFloat(2, 2500, 14000),
                    'mileage' => fake()->numberBetween(18000, 180000),
                    'production_year' => fake()->numberBetween(2010, (int) now()->year),
                ], $this->randomTags($tagIds, 1, 4), $createdAt);
            }
        }
    }

    private function createSeededCar(User $user, array $attributes, array $tagIds = [], $createdAt = null, $soldAt = null): Car
    {
        $car = Car::factory()->create([
            ...$attributes,
            'user_id' => $user->id,
        ]);

        $timestamps = [
            'created_at' => $createdAt ?? now(),
            'updated_at' => $soldAt ?? $createdAt ?? now(),
            'sold_at' => $soldAt,
        ];

        Car::query()->whereKey($car->id)->update($timestamps);

        if ($tagIds !== []) {
            $car->tags()->attach($tagIds);
        }

        return $car->refresh();
    }

    private function randomTags(array $tagIds, int $minimum, int $maximum): array
    {
        if ($tagIds === []) {
            return [];
        }

        $count = fake()->numberBetween($minimum, min($maximum, count($tagIds)));

        return fake()->randomElements($tagIds, $count);
    }
}
