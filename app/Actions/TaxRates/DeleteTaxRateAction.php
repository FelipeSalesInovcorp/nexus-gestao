<?php

namespace App\Actions\TaxRates;

use App\Models\TaxRate;

class DeleteTaxRateAction
{
    public function execute(TaxRate $taxRate): void
    {
        $taxRate->delete();
    }
}
