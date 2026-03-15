<?php

namespace App\Http\Controllers;

use App\Models\Car;
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

    public function myOffers(Request $request): View
    {
        $cars = $request->user()
            ->cars()
            ->latest()
            ->paginate(12);

        return view('cars.my-offers', compact('cars'));
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
}
