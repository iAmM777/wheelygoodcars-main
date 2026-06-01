<?php

namespace Tests\Feature;

use App\Models\Car;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SuspiciousProvidersTest extends TestCase
{
    use RefreshDatabase;

    public function test_suspicious_providers_page_lists_expected_flags(): void
    {
        $tag = Tag::create(['name' => 'Goed onderhouden']);

        $noPhoneUser = User::factory()->create([
            'phone_number' => null,
            'name' => 'Geen Telefoon',
        ]);
        $this->createCar($noPhoneUser, ['price' => 5200, 'mileage' => 56000, 'production_year' => 2018], [$tag->id]);

        $oldLowMileageUser = User::factory()->create([
            'phone_number' => '0612345678',
            'name' => 'Oud En Sluik',
        ]);
        $this->createCar($oldLowMileageUser, ['price' => 8400, 'mileage' => 18000, 'production_year' => 2008], [$tag->id]);

        $fastSellUser = User::factory()->create([
            'phone_number' => '0623456789',
            'name' => 'Snel Verkopen',
        ]);
        foreach (range(1, 4) as $index) {
            $createdAt = now()->subDays(10)->startOfDay();
            $this->createCar($fastSellUser, ['price' => 14500, 'mileage' => 42000 + ($index * 1000), 'production_year' => 2017], [$tag->id], $createdAt, (clone $createdAt)->addHours(5));
        }

        $lowPriceUser = User::factory()->create([
            'phone_number' => '0634567890',
            'name' => 'Spotprijs',
        ]);
        $this->createCar($lowPriceUser, ['price' => 650, 'mileage' => 150000, 'production_year' => 2011], [$tag->id]);
        $this->createCar($lowPriceUser, ['price' => 850, 'mileage' => 120000, 'production_year' => 2013], [$tag->id]);

        $noTagUser = User::factory()->create([
            'phone_number' => '0645678901',
            'name' => 'Geen Tags',
        ]);
        $this->createCar($noTagUser, ['price' => 7600, 'mileage' => 78000, 'production_year' => 2019], []);

        $dormantUser = User::factory()->create([
            'phone_number' => '0656789012',
            'name' => 'Stilgevallen',
        ]);
        $this->createCar($dormantUser, ['price' => 9400, 'mileage' => 99000, 'production_year' => 2014], [$tag->id], now()->subMonths(14));

        $response = $this->actingAs($noPhoneUser)->get(route('admin.suspicious-providers'));

        $response->assertOk();
        $response->assertSee('Opvallende aanbieders');
        $response->assertSee('Geen Telefoon');
        $response->assertSee('Geen telefoonnummer');
        $response->assertSee('Oud model + weinig km');
        $response->assertSee('4+ snelle verkopen > €10.000');
        $response->assertSee("Alleen auto's onder €1.000");
        $response->assertSee('Geen tags gebruikt');
        $response->assertSee('Geen nieuwe auto’s in 13+ maanden');
    }

    private function createCar(User $user, array $attributes, array $tagIds = [], $createdAt = null, $soldAt = null): Car
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
}