<?php

namespace App\Http\Controllers\Config;

use App\Actions\Products\CreateProductAction;
use App\Actions\Products\DeleteProductAction;
use App\Actions\Products\ListProductsAction;
use App\Actions\Products\UpdateProductAction;
use App\Models\Product;
use App\Models\TaxRate;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ProductController
{
    public function index(Request $request, ListProductsAction $action)
    {
        $filters = [
            'search' => $request->query('search'),
            'active' => $request->query('active'),
        ];

        return Inertia::render('Config/Products/Index', [
            'filters' => $filters,
            'products' => $action->execute($filters),
            'taxRates' => TaxRate::orderBy('name')->get(['id','name','rate']),
        ]);
    }

    public function create()
    {
        return Inertia::render('Config/Products/Create', [
            'taxRates' => TaxRate::orderBy('name')->get(['id','name','rate']),
        ]);
    }

    public function store(Request $request, CreateProductAction $action)
    {
        $data = $request->validate([
            'sku' => ['nullable','string','max:50'],
            'name' => ['required','string','max:255'],
            'description' => ['nullable','string'],
            'price' => ['required','numeric','min:0'],
            'reference' => ['nullable', 'string', 'max:50'],
            'tax_rate_id' => ['required','exists:tax_rates,id'],
            'active' => ['boolean'],
        ]);

        $data['active'] = (bool)($data['active'] ?? true);

        $action->execute($data);

        return redirect()->route('products.index');
    }

    public function edit(Product $product)
    {
        return Inertia::render('Config/Products/Edit', [
            'product' => $product->only(['id','sku','name','description','price','tax_rate_id','active']),
            'taxRates' => TaxRate::orderBy('name')->get(['id','name','rate']),
        ]);
    }

    public function update(Request $request, Product $product, UpdateProductAction $action)
    {
        $data = $request->validate([
            'sku' => ['nullable','string','max:50'],
            'name' => ['required','string','max:255'],
            'description' => ['nullable','string'],
            'price' => ['required','numeric','min:0'],
            'reference' => ['nullable', 'string', 'max:50'],
            'tax_rate_id' => ['required','exists:tax_rates,id'],
            'active' => ['boolean'],
        ]);

        $data['active'] = (bool)($data['active'] ?? false);

        $action->execute($product, $data);

        return redirect()->route('products.index');
    }

    public function destroy(Product $product, DeleteProductAction $action)
    {
        $action->execute($product);

        return redirect()->back();
    }
}
