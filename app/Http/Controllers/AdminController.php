<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Contracts\View\View;

class AdminController extends Controller
{
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
}