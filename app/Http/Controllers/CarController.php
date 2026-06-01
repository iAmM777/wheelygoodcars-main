<?php

namespace App\Http\Controllers;

use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Car;
use App\Models\Tag;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class CarController extends Controller
{
    public function index(): View
    {
        return view('cars.index');
    }

    public function show(Car $car): View
    {
        // Prevent viewing sold cars in detail (guests shouldn't see them)
        abort_if($car->sold_at !== null, 404);

        // Increment view count
        $car->increment('views');

        if ($car->views_today_date?->isToday()) {
            $car->increment('views_today');
        } else {
            $car->forceFill([
                'views_today' => 1,
                'views_today_date' => now()->toDateString(),
            ])->save();
        }

        // Ensure tags are loaded for the detail view
        $car->load(['tags', 'user']);

        return view('cars.show', compact('car'));
    }

    public function pdf(Car $car)
    {
        abort_if($car->sold_at !== null, 404);

        $car->loadMissing(['tags', 'user']);

        $fileName = Str::slug(trim($car->brand.' '.$car->model.' '.$car->license_plate)).'.pdf';

        return Pdf::loadView('cars.pdf', [
            'car' => $car,
        ])->stream($fileName);
    }

    public function myOffers(Request $request): View
    {
        $userCars = $request->user()->cars();

        $cars = (clone $userCars)
            ->with('tags')
            ->latest()
            ->paginate(12);

        $stats = [
            'total' => (clone $userCars)->count(),
            'active' => (clone $userCars)->whereNull('sold_at')->count(),
            'sold' => (clone $userCars)->whereNotNull('sold_at')->count(),
        ];

        return view('cars.my-offers', compact('cars', 'stats'));
    }

    public function tagStatistics(): View
    {
        $tags = Tag::query()
            ->withCount([
                'cars',
                'cars as active_cars_count' => function ($query): void {
                    $query->whereNull('sold_at');
                },
                'cars as sold_cars_count' => function ($query): void {
                    $query->whereNotNull('sold_at');
                },
            ])
            ->orderByDesc('cars_count')
            ->orderBy('name')
            ->get();

        $totals = [
            'tags' => $tags->count(),
            'usage' => $tags->sum('cars_count'),
            'active' => $tags->sum('active_cars_count'),
            'sold' => $tags->sum('sold_cars_count'),
        ];

        return view('cars.tag-statistics', compact('tags', 'totals'));
    }

    public function markAsSold(Request $request, Car $car): RedirectResponse|JsonResponse
    {
        $this->ensureOwner($request, $car);

        $car->update([
            'sold_at' => now(),
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'status' => 'sold',
                'sold_at' => $car->sold_at?->toIso8601String(),
            ]);
        }

        return redirect()
            ->route('cars.my-offers')
            ->with('status', 'Auto gemarkeerd als verkocht.');
    }

    public function markAsActive(Request $request, Car $car): RedirectResponse|JsonResponse
    {
        $this->ensureOwner($request, $car);

        $car->update([
            'sold_at' => null,
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'status' => 'active',
                'sold_at' => null,
            ]);
        }

        return redirect()
            ->route('cars.my-offers')
            ->with('status', 'Auto weer actief in je aanbod.');
    }

    public function destroy(Request $request, Car $car): RedirectResponse
    {
        $this->ensureOwner($request, $car);

        $car->delete();

        return redirect()
            ->route('cars.my-offers')
            ->with('status', 'Auto verwijderd uit je aanbod.');
    }

    public function edit(Request $request, Car $car): View
    {
        $this->ensureOwner($request, $car);

        return view('cars.edit', [
            'car' => $car->load('tags'),
            'tags' => Tag::query()->orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, Car $car): RedirectResponse
    {
        $this->ensureOwner($request, $car);

        $data = $request->validate([
            'price' => ['required', 'numeric', 'min:0'],
            'sold' => ['nullable', 'boolean'],
            'tags' => ['nullable', 'array'],
            'tags.*' => ['integer', 'exists:tags,id'],
        ]);

        $sold = (bool) ($request->input('sold') ?? false);
        $tagIds = $data['tags'] ?? [];

        unset($data['tags']);

        $car->price = $data['price'];
        $car->sold_at = $sold ? ($car->sold_at ?? now()) : null;
        $car->save();

        $car->tags()->sync($tagIds);

        return redirect()
            ->route('cars.my-offers')
            ->with('status', 'Aanbieding bijgewerkt.');
    }

    public function createStepOne(): View
    {
        return view('cars.create-step-1');
    }

    public function storeStepOne(Request $request): RedirectResponse
    {
        $request->validate([
            'license_plate' => ['required', 'string', 'max:20'],
        ]);

        $licensePlate = $this->normalizeLicensePlate($request->input('license_plate'));

        if (! preg_match('/^[A-Z0-9]{6}$/', $licensePlate)) {
            return back()
                ->withInput()
                ->withErrors([
                    'license_plate' => 'Voer een geldig kenteken in (6 tekens, zonder spaties).',
                ]);
        }

        $alreadyOffered = Car::query()
            ->where('license_plate', $licensePlate)
            ->whereNull('sold_at')
            ->exists();

        if ($alreadyOffered) {
            return back()
                ->withInput()
                ->withErrors([
                    'license_plate' => 'Dit kenteken staat al actief te koop.',
                ]);
        }

        session(['car_offer.license_plate' => $licensePlate]);

        return redirect()
            ->route('cars.create.step2')
            ->with('status', 'Kenteken gecontroleerd. Vul nu de overige gegevens in.');
    }

    public function createStepTwo(): View|RedirectResponse
    {
        $licensePlate = session('car_offer.license_plate');

        if (! $licensePlate) {
            return redirect()
                ->route('cars.create.step1')
                ->withErrors([
                    'license_plate' => 'Start met stap 1 en voer eerst een kenteken in.',
                ]);
        }

        return view('cars.create-step-2', [
            'licensePlate' => $licensePlate,
            'tags' => Tag::query()->orderBy('name')->get(),
        ]);
    }

    public function storeStepTwo(Request $request): RedirectResponse
    {
        $licensePlate = session('car_offer.license_plate');

        if (! $licensePlate) {
            return redirect()
                ->route('cars.create.step1')
                ->withErrors([
                    'license_plate' => 'Je sessie is verlopen. Start opnieuw met stap 1.',
                ]);
        }

        $validated = $request->validate([
            'brand' => ['required', 'string', 'max:255'],
            'model' => ['required', 'string', 'max:255'],
            'price' => ['required', 'numeric', 'min:0'],
            'mileage' => ['required', 'integer', 'min:0'],
            'seats' => ['nullable', 'integer', 'min:1'],
            'doors' => ['nullable', 'integer', 'min:1'],
            'production_year' => ['nullable', 'integer', 'min:1900', 'max:2100'],
            'weight' => ['nullable', 'integer', 'min:0'],
            'color' => ['nullable', 'string', 'max:255'],
            'tags' => ['nullable', 'array'],
            'tags.*' => ['integer', 'exists:tags,id'],
        ]);

        $tagIds = $validated['tags'] ?? [];

        unset($validated['tags']);

        $car = Car::create([
            ...$validated,
            'user_id' => $request->user()->id,
            'license_plate' => $licensePlate,
        ]);

        if ($tagIds !== []) {
            $car->tags()->sync($tagIds);
        }

        $request->session()->forget('car_offer');

        return redirect()
            ->route('cars.my-offers')
            ->with('status', 'Je auto is succesvol aangeboden.');
    }

    private function normalizeLicensePlate(string $licensePlate): string
    {
        return strtoupper((string) preg_replace('/[^A-Za-z0-9]/', '', $licensePlate));
    }

    private function ensureOwner(Request $request, Car $car): void
    {
        abort_unless((int) $car->user_id === (int) $request->user()->id, 403);
    }
}
