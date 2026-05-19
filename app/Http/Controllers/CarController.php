<?php

namespace App\Http\Controllers;

use App\Models\Car;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CarController extends Controller
{
    public function index(): View
    {
        $cars = Car::query()
            ->whereNull('sold_at')
            ->latest()
            ->paginate(12);

        return view('cars.index', compact('cars'));
    }

    public function show(Car $car): View
    {
        // Prevent viewing sold cars in detail (guests shouldn't see them)
        abort_if($car->sold_at !== null, 404);

        // Increment view count
        $car->increment('views');

        return view('cars.show', compact('car'));
    }

    public function myOffers(Request $request): View
    {
        $userCars = $request->user()->cars();

        $cars = (clone $userCars)
            ->latest()
            ->paginate(12);

        $stats = [
            'total' => (clone $userCars)->count(),
            'active' => (clone $userCars)->whereNull('sold_at')->count(),
            'sold' => (clone $userCars)->whereNotNull('sold_at')->count(),
        ];

        return view('cars.my-offers', compact('cars', 'stats'));
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

        return view('cars.edit', compact('car'));
    }

    public function update(Request $request, Car $car): RedirectResponse
    {
        $this->ensureOwner($request, $car);

        $data = $request->validate([
            'price' => ['required', 'numeric', 'min:0'],
            'sold' => ['nullable', 'boolean'],
        ]);

        $sold = (bool) ($request->input('sold') ?? false);

        $car->price = $data['price'];
        $car->sold_at = $sold ? ($car->sold_at ?? now()) : null;
        $car->save();

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
        ]);

        Car::create([
            ...$validated,
            'user_id' => $request->user()->id,
            'license_plate' => $licensePlate,
        ]);

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
