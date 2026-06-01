<?php

namespace Tests\Feature;

use App\Models\Car;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TagStatisticsTest extends TestCase
{
    use RefreshDatabase;

    public function test_tag_statistics_page_shows_active_and_sold_counts(): void
    {
        $user = User::factory()->create();
        $tagPopular = Tag::create(['name' => 'Populair']);
        $tagNiche = Tag::create(['name' => 'Niche']);

        $activeCar = Car::factory()->create([
            'user_id' => $user->id,
            'sold_at' => null,
        ]);
        $soldCar = Car::factory()->create([
            'user_id' => $user->id,
            'sold_at' => now(),
        ]);

        $activeCar->tags()->attach([$tagPopular->id, $tagNiche->id]);
        $soldCar->tags()->attach($tagPopular->id);

        $response = $this->actingAs($user)->get(route('tags.statistics'));

        $response->assertOk();
        $response->assertSee('Tag statistieken');
        $response->assertSee('Populair');
        $response->assertSee('2');
        $response->assertSee('1');
        $response->assertSee('Niche');
    }
}