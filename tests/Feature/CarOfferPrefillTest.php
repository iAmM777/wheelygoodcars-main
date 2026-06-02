<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class CarOfferPrefillTest extends TestCase
{
    use RefreshDatabase;

    public function test_step_one_fetches_rdw_data_and_prefills_step_two(): void
    {
        Http::fake([
            'opendata.rdw.nl/*' => Http::response([
                [
                    'merk' => 'Volkswagen',
                    'handelsbenaming' => 'Golf',
                    'datum_eerste_toelating' => '2019-03-14T00:00:00.000',
                    'aantal_zitplaatsen' => '5',
                    'aantal_deuren' => '5',
                    'massa_ledig_voertuig' => '1280',
                    'eerste_kleur' => 'ZWART',
                    'tweede_kleur' => null,
                ],
            ], 200),
        ]);

        $user = User::factory()->create();

        $this->actingAs($user)
            ->post(route('cars.create.step1.store'), [
                'license_plate' => '12-ab-34',
            ])
            ->assertRedirect(route('cars.create.step2'));

        $this->actingAs($user)
            ->get(route('cars.create.step2'))
            ->assertOk()
            ->assertSee('Volkswagen')
            ->assertSee('Golf')
            ->assertSee('2019')
            ->assertSee('5')
            ->assertSee('1280')
            ->assertSee('ZWART')
            ->assertSee('We hebben alvast gegevens uit de RDW API ingevuld.');
    }
}