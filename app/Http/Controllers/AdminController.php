<?php

namespace App\Http\Controllers;

use App\Models\Car;
use App\Models\User;
use Carbon\CarbonPeriod;
use Illuminate\Http\JsonResponse;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Carbon;

class AdminController extends Controller
{
    public function dashboard(): View
    {
        return view('admin.dashboard', [
            'dashboard' => $this->buildDashboardPayload(),
        ]);
    }

    public function dashboardData(): JsonResponse
    {
        return response()->json($this->buildDashboardPayload());
    }

    public function suspiciousProviders(): View
    {
        $providers = User::query()
            ->with(['cars.tags'])
            ->orderBy('name')
            ->get()
            ->map(function (User $user): ?array {
                $cars = $user->cars->sortByDesc('created_at')->values();

                if ($cars->isEmpty()) {
                    return null;
                }

                $flags = [];

                if (blank($user->phone_number)) {
                    $flags[] = [
                        'label' => 'Geen telefoonnummer',
                        'description' => 'Contactgegevens ontbreken',
                        'type' => 'warning',
                    ];
                }

                $oldLowMileageCars = $cars->filter(function ($car): bool {
                    return $car->production_year !== null
                        && $car->production_year <= (int) now()->subYears(10)->format('Y')
                        && (int) $car->mileage <= 50000;
                });

                if ($oldLowMileageCars->isNotEmpty()) {
                    $flags[] = [
                        'label' => 'Oud model + weinig km',
                        'description' => $oldLowMileageCars->count().' auto(s) passen in deze combinatie',
                        'type' => 'warning',
                    ];
                }

                $sameDaySoldHighPriceCars = $cars->filter(function ($car): bool {
                    return $car->sold_at !== null
                        && $car->created_at?->toDateString() === $car->sold_at?->toDateString()
                        && (float) $car->price > 10000;
                });

                if ($sameDaySoldHighPriceCars->count() > 3) {
                    $flags[] = [
                        'label' => '4+ snelle verkopen > €10.000',
                        'description' => $sameDaySoldHighPriceCars->count() . " auto's verkocht op dag van toevoegen",
                        'type' => 'danger',
                    ];
                }

                if ($cars->every(function ($car): bool {
                    return (float) $car->price < 1000;
                })) {
                    $flags[] = [
                        'label' => "Alleen auto's onder €1.000",
                        'description' => 'De volledige voorraad is opvallend goedkoop',
                        'type' => 'warning',
                    ];
                }

                if ($cars->every(function ($car): bool {
                    return $car->tags->isEmpty();
                })) {
                    $flags[] = [
                        'label' => 'Geen tags gebruikt',
                        'description' => 'Geen enkele auto heeft tags',
                        'type' => 'secondary',
                    ];
                }

                $latestOfferAt = $cars->first()?->created_at;

                if ($latestOfferAt !== null && $latestOfferAt->lt(now()->subMonths(13))) {
                    $flags[] = [
                        'label' => 'Geen nieuwe auto’s in 13+ maanden',
                        'description' => 'Laatste toevoeging ligt meer dan 13 maanden terug',
                        'type' => 'secondary',
                    ];
                }

                if ($flags === []) {
                    return null;
                }

                return [
                    'user' => $user,
                    'cars_count' => $cars->count(),
                    'active_count' => $cars->whereNull('sold_at')->count(),
                    'sold_count' => $cars->whereNotNull('sold_at')->count(),
                    'latest_offer_at' => $latestOfferAt,
                    'flags' => $flags,
                    'flag_count' => count($flags),
                ];
            })
            ->filter()
            ->sortByDesc('flag_count')
            ->values();

        $summary = [
            'providers' => $providers->count(),
            'flags' => $providers->sum('flag_count'),
            'cars' => $providers->sum('cars_count'),
        ];

        return view('admin.suspicious-providers', compact('providers', 'summary'));
    }

    private function buildDashboardPayload(): array
    {
        $today = Carbon::today();
        $periodStart = $today->copy()->subDays(13);
        $period = collect(CarbonPeriod::create($periodStart, $today))->map(fn (Carbon $date): string => $date->toDateString())->values();

        $totalCars = Car::query()->count();
        $soldCars = Car::query()->whereNotNull('sold_at')->count();
        $activeCars = $totalCars - $soldCars;
        $offeredToday = Car::query()->whereDate('created_at', $today)->count();
        $providers = User::query()->has('cars')->count();
        $viewsToday = (int) Car::query()->whereDate('views_today_date', $today)->sum('views_today');
        $averageCarsPerProvider = $providers > 0 ? round($totalCars / $providers, 1) : 0.0;
        $soldPercentage = $totalCars > 0 ? round(($soldCars / $totalCars) * 100, 1) : 0.0;

        $offersByDay = Car::query()
            ->selectRaw('DATE(created_at) as day, COUNT(*) as total')
            ->whereBetween('created_at', [$periodStart->startOfDay(), $today->endOfDay()])
            ->groupBy('day')
            ->pluck('total', 'day');

        $soldByDay = Car::query()
            ->selectRaw('DATE(sold_at) as day, COUNT(*) as total')
            ->whereNotNull('sold_at')
            ->whereBetween('sold_at', [$periodStart->startOfDay(), $today->endOfDay()])
            ->groupBy('day')
            ->pluck('total', 'day');

        $viewsByDay = Car::query()
            ->selectRaw('DATE(views_today_date) as day, SUM(views_today) as total')
            ->whereNotNull('views_today_date')
            ->whereBetween('views_today_date', [$periodStart, $today])
            ->groupBy('day')
            ->pluck('total', 'day')
            ->all();

        $topProviders = User::query()
            ->has('cars')
            ->withCount('cars')
            ->orderByDesc('cars_count')
            ->orderBy('name')
            ->limit(5)
            ->get();

        return [
            'generated_at' => now()->toIso8601String(),
            'metrics' => [
                'total_cars' => $totalCars,
                'sold_cars' => $soldCars,
                'active_cars' => $activeCars,
                'today_offered' => $offeredToday,
                'providers' => $providers,
                'today_views' => $viewsToday,
                'average_cars_per_provider' => $averageCarsPerProvider,
                'sold_ratio' => $soldPercentage,
            ],
            'charts' => [
                'status' => [
                    'labels' => ['Actief', 'Verkocht'],
                    'values' => [$activeCars, $soldCars],
                ],
                'daily' => [
                    'labels' => $period->all(),
                    'offers' => $period->map(fn (string $date): int => (int) ($offersByDay[$date] ?? 0))->all(),
                    'sold' => $period->map(fn (string $date): int => (int) ($soldByDay[$date] ?? 0))->all(),
                    'views' => $period->map(fn (string $date): int => (int) ($viewsByDay[$date] ?? 0))->all(),
                ],
                'providers' => [
                    'labels' => $topProviders->pluck('name')->all(),
                    'values' => $topProviders->pluck('cars_count')->all(),
                ],
            ],
        ];
    }
}