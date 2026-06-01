<?php

namespace Tests\Feature;

use App\Models\Car;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminDashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_page_and_data_endpoint_return_realtime_metrics(): void
    {
        $user = User::factory()->create();

        Car::factory()->create([
            'user_id' => $user->id,
            'created_at' => now(),
            'updated_at' => now(),
            'sold_at' => null,
            'views_today' => 3,
            'views_today_date' => now()->toDateString(),
        ]);

        Car::factory()->create([
            'user_id' => $user->id,
            'created_at' => now()->subDays(2),
            'updated_at' => now()->subDays(1),
            'sold_at' => now()->subDays(1),
            'views_today' => 1,
            'views_today_date' => now()->toDateString(),
        ]);

        $page = $this->actingAs($user)->get(route('admin.dashboard'));
        $page->assertOk();
        $page->assertSee('Aanbod dashboard');

        $response = $this->actingAs($user)->getJson(route('admin.dashboard.data'));

        $response->assertOk();
        $response->assertJsonPath('metrics.total_cars', 2);
        $response->assertJsonPath('metrics.sold_cars', 1);
        $response->assertJsonPath('metrics.today_offered', 1);
        $response->assertJsonPath('metrics.providers', 1);
        $response->assertJsonPath('metrics.today_views', 4);
        $response->assertJsonPath('metrics.average_cars_per_provider', 2);

        $viewsTrend = $response->json('charts.daily.views');

        $this->assertCount(14, $viewsTrend);
        $this->assertSame(4, $viewsTrend[array_key_last($viewsTrend)]);
        $this->assertSame(4, collect($viewsTrend)->sum());

        $response->assertJsonStructure([
            'generated_at',
            'metrics' => [
                'total_cars',
                'sold_cars',
                'active_cars',
                'today_offered',
                'providers',
                'today_views',
                'average_cars_per_provider',
                'sold_ratio',
            ],
            'charts' => [
                'status' => ['labels', 'values'],
                'daily' => ['labels', 'offers', 'sold', 'views'],
                'providers' => ['labels', 'values'],
            ],
        ]);
    }
}