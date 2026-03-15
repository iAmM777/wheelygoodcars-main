<?php

namespace Database\Factories;

use App\Models\Car;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Car>
 */
class CarFactory extends Factory
{
    protected $model = Car::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'license_plate' => strtoupper(fake()->bothify('##??##')),
            'brand' => fake()->randomElement(['Volkswagen', 'Toyota', 'BMW', 'Audi', 'Kia', 'Ford', 'Renault', 'Peugeot']),
            'model' => fake()->bothify('Model-??##'),
            'price' => fake()->randomFloat(2, 2000, 65000),
            'mileage' => fake()->numberBetween(5000, 280000),
            'seats' => fake()->randomElement([2, 4, 5, 7]),
            'doors' => fake()->randomElement([2, 3, 4, 5]),
            'production_year' => fake()->numberBetween(2000, (int) date('Y')),
            'weight' => fake()->numberBetween(900, 2500),
            'color' => fake()->safeColorName(),
            'image' => null,
            'sold_at' => null,
            'views' => fake()->numberBetween(0, 200),
        ];
    }
}
