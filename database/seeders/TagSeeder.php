<?php

namespace Database\Seeders;

use App\Models\Tag;
use Illuminate\Database\Seeder;

class TagSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tags = [
            'Goed onderhouden',
            'Nieuw model',
            'Goedkoop',
            'Lage kilometerstand',
            'Elektrisch',
            'Hybrid',
            'Automaat',
            'Handgeschakeld',
            'Airco',
            'Cruise control',
            'Panoramadak',
            'Lederen interieur',
            'Sportief',
            'Familie-auto',
            'Duurzaam',
            'Krachtig',
            'Comfortabel',
            'Draagvermogen',
            'Terreinwagen',
            'Caravan-trailer',
        ];

        foreach ($tags as $tagName) {
            Tag::firstOrCreate(['name' => $tagName]);
        }
    }
}
