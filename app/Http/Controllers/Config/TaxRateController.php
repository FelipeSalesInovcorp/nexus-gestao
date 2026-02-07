<?php

namespace App\Http\Controllers\Config;

use App\Actions\TaxRates\CreateTaxRateAction;
use App\Actions\TaxRates\DeleteTaxRateAction;
use App\Actions\TaxRates\ListTaxRatesAction;
use App\Http\Controllers\Controller;
use App\Models\TaxRate;
use Illuminate\Http\Request;
use Inertia\Inertia;

class TaxRateController extends Controller
{
    public function index(Request $request, ListTaxRatesAction $action)
    {
        $filters = [
            'search' => $request->query('search'),
        ];

        return Inertia::render('Config/TaxRates/Index', [
            'filters' => $filters,
            'taxRates' => $action->execute($filters),
        ]);
    }

    public function create()
    {
        return Inertia::render('Config/TaxRates/Create');
    }

    public function store(Request $request, CreateTaxRateAction $action)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'rate' => ['required', 'numeric', 'min:0', 'max:100'],
            'active' => ['sometimes', 'boolean'],
        ]);

        $action->execute($data);

        return redirect()->route('tax-rates.index');
    }

    public function destroy(TaxRate $taxRate, DeleteTaxRateAction $action)
    {
        $action->execute($taxRate);

        return redirect()->back();
    }
}

