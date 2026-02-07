<?php

namespace App\Actions\TaxRates;

use App\Models\TaxRate;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ListTaxRatesAction
{
    public function execute(array $filters = []): LengthAwarePaginator
    {
        $search = $filters['search'] ?? null;

        return TaxRate::query()
            ->when($search, fn ($q) => $q->where('name', 'like', "%{$search}%"))
            ->orderByDesc('active')
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString()
            ->through(fn (TaxRate $t) => [
                'id' => $t->id,
                'name' => $t->name,
                'rate' => (string) $t->rate,
                'active' => (bool) $t->active,
            ]);
    }
}
