<?php

namespace App\Actions\Products;

use App\Models\Product;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ListProductsAction
{
    public function execute(array $filters): LengthAwarePaginator
    {
        $search = $filters['search'] ?? null;
        $active = $filters['active'] ?? null;

        return Product::query()
            ->with('taxRate:id,name,rate')
            ->when($search, function ($q) use ($search) {
                $q->where(function ($qq) use ($search) {
                    $qq->where('name', 'like', "%{$search}%")
                       ->orWhere('sku', 'like', "%{$search}%");
                });
            })
            ->when($active !== null && $active !== '', function ($q) use ($active) {
                $q->where('active', (bool) $active);
            })
            ->orderByDesc('id')
            ->paginate(15)
            ->withQueryString()
            ->through(fn ($p) => [
                'id' => $p->id,
                'sku' => $p->sku,
                'name' => $p->name,
                'price' => $p->price,
                'active' => (bool) $p->active,
                'tax_rate' => $p->taxRate ? [
                    'id' => $p->taxRate->id,
                    'name' => $p->taxRate->name,
                    'rate' => $p->taxRate->rate,
                ] : null,
            ]);
    }
}
