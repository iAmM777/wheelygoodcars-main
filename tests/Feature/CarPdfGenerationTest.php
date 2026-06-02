<?php

namespace Tests\Feature;

use App\Models\Car;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CarPdfGenerationTest extends TestCase
{
    use RefreshDatabase;

    public function test_pdf_route_returns_a_pdf_response(): void
    {
        $user = User::factory()->create();
        $tag = Tag::create(['name' => 'Prijsknaller']);

        $car = Car::factory()->create([
            'user_id' => $user->id,
        ]);

        $car->tags()->attach($tag->id);

        $response = $this->get(route('cars.pdf', $car));

        $response->assertOk();
        $response->assertHeader('content-type', 'application/pdf');
    }

    public function test_edit_and_show_pages_link_to_generated_pdf(): void
    {
        $user = User::factory()->create();

        $car = Car::factory()->create([
            'user_id' => $user->id,
        ]);

        $this->actingAs($user)
            ->get(route('cars.edit', $car))
            ->assertOk()
            ->assertSee('Genereer PDF');

        $this->get(route('cars.show', $car))
            ->assertOk()
            ->assertSee('PDF voor printen')
            ->assertSee(route('cars.pdf', $car));
    }

    public function test_my_offers_and_show_pages_display_view_counts(): void
    {
        $user = User::factory()->create();

        $car = Car::factory()->create([
            'user_id' => $user->id,
            'views' => 12,
            'views_today' => 3,
        ]);

        $this->actingAs($user)
            ->get(route('cars.my-offers'))
            ->assertOk()
            ->assertSee('Views');

        $this->actingAs($user)
            ->get(route('cars.show', $car))
            ->assertOk()
            ->assertSee('Totale views')
            ->assertSee('Views vandaag');
    }
}