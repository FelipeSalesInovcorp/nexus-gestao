<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Country;

class CountrySeeder extends Seeder
{
    public function run(): void
    {
        $countries = [
            ['name' => 'Portugal', 'iso2' => 'PT'],
            ['name' => 'Espanha', 'iso2' => 'ES'],
            ['name' => 'França', 'iso2' => 'FR'],
            ['name' => 'Alemanha', 'iso2' => 'DE'],
            ['name' => 'Reino Unido', 'iso2' => 'GB'],
            ['name' => 'Brasil', 'iso2' => 'BR'],
            ['name' => 'Angola', 'iso2' => 'AO'],
            ['name' => 'Moçambique', 'iso2' => 'MZ'],
            ['name' => 'Cabo Verde', 'iso2' => 'CV'],
        ];

        foreach ($countries as $c) {
            Country::updateOrCreate(
                ['iso2' => $c['iso2']],
                ['name' => $c['name']]
            );
        }
    }
}
