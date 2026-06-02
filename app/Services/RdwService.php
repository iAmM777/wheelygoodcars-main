<?php

namespace App\Services;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use Throwable;

class RdwService
{
    public function fetchCarData(string $licensePlate): array
    {
        try {
            $response = Http::acceptJson()
                ->timeout(5)
                ->retry(1, 200)
                ->get('https://opendata.rdw.nl/resource/m9d7-ebf2.json', [
                    'kenteken' => $licensePlate,
                    '$limit' => 1,
                ]);

            if (! $response->successful()) {
                return [];
            }

            $data = $response->json();
            $car = is_array($data) ? ($data[0] ?? null) : null;

            if (! is_array($car)) {
                return [];
            }

            return array_filter([
                'brand' => $this->stringValue($car['merk'] ?? null),
                'model' => $this->stringValue($car['handelsbenaming'] ?? null),
                'production_year' => $this->yearValue($car['datum_eerste_toelating'] ?? null),
                'seats' => $this->intValue($car['aantal_zitplaatsen'] ?? null),
                'doors' => $this->intValue($car['aantal_deuren'] ?? null),
                'weight' => $this->intValue($car['massa_ledig_voertuig'] ?? null),
                'color' => $this->composeColor($car['eerste_kleur'] ?? null, $car['tweede_kleur'] ?? null),
            ], static fn ($value) => $value !== null && $value !== '');
        } catch (ConnectionException|Throwable) {
            return [];
        }
    }

    private function stringValue(mixed $value): ?string
    {
        $value = is_string($value) ? trim($value) : null;

        return $value !== '' ? $value : null;
    }

    private function intValue(mixed $value): ?int
    {
        return is_numeric($value) ? (int) $value : null;
    }

    private function yearValue(mixed $value): ?int
    {
        if (! is_string($value) || $value === '') {
            return null;
        }

        $year = (int) substr($value, 0, 4);

        return $year > 0 ? $year : null;
    }

    private function composeColor(mixed $firstColor, mixed $secondColor): ?string
    {
        $colors = array_filter([
            $this->stringValue($firstColor),
            $this->stringValue($secondColor),
        ]);

        if ($colors === []) {
            return null;
        }

        return implode(' / ', $colors);
    }
}