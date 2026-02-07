<?php

namespace App\Actions\TaxRates;

use App\Models\TaxRate;

class CreateTaxRateAction
{
    public function execute(array $data): TaxRate
    {
        return TaxRate::create([
            'name' => $data['name'],
            'rate' => $data['rate'],
            'active' => $data['active'] ?? true,
        ]);
    }
}
