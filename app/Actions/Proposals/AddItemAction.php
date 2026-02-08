<?php

namespace App\Actions\Proposals;

use App\Models\Product;
use App\Models\Proposal;
use App\Models\ProposalItem;

class AddItemAction
{
    public function __construct(private RecalculateProposalTotalsAction $recalc) {}

    public function execute(Proposal $proposal, array $data): ProposalItem
    {
        $product = Product::with('taxRate:id,name,rate')->findOrFail($data['product_id']);

        $qty = (float)($data['qty'] ?? 1);
        $unit = (float)($data['unit_price'] ?? $product->price);
        $taxRate = $product->taxRate?->rate ?? 0;

        $lineSubtotal = $qty * $unit;
        $lineTax = $lineSubtotal * ($taxRate / 100);
        $lineTotal = $lineSubtotal + $lineTax;

        $item = $proposal->items()->create([
            'product_id' => $product->id,
            'description' => $product->name,
            'qty' => $qty,
            'unit_price' => $unit,
            'tax_rate_id' => $product->tax_rate_id,
            'tax_rate' => $taxRate,

            'supplier_id' => $data['supplier_id'] ?? null,
            'cost_price' => $data['cost_price'] ?? null,

            'line_subtotal' => $lineSubtotal,
            'line_tax' => $lineTax,
            'line_total' => $lineTotal,
        ]);

        $this->recalc->execute($proposal);

        return $item;
    }
}
