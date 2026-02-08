<?php

namespace App\Actions\Proposals;

use App\Models\Proposal;
use App\Models\ProposalItem;
use Illuminate\Support\Facades\DB;

class UpdateProposalAction
{
    public function execute(Proposal $proposal, array $data): Proposal
    {
        return DB::transaction(function () use ($proposal, $data) {
            $proposal->update([
                'proposal_date' => $data['proposal_date'],
                'valid_until' => $data['valid_until'],
                'entity_id' => $data['entity_id'],
                'status' => $data['status'],
                'notes' => $data['notes'] ?? null,
            ]);

            // substituir linhas (simples e seguro p/ prazo)
            $proposal->items()->delete();

            $subtotal = 0; $taxTotal = 0; $total = 0;

            foreach ($data['items'] as $item) {
                $qty = (float)($item['qty'] ?? 1);
                $unit = (float)($item['unit_price'] ?? 0);
                $taxRate = (float)($item['tax_rate'] ?? 0);

                $lineSubtotal = round($qty * $unit, 2);
                $lineTax = round($lineSubtotal * ($taxRate / 100), 2);
                $lineTotal = round($lineSubtotal + $lineTax, 2);

                ProposalItem::create([
                    'proposal_id' => $proposal->id,
                    'product_id' => $item['product_id'] ?? null,
                    'description' => $item['description'],
                    'qty' => $qty,
                    'unit_price' => $unit,
                    'tax_rate_id' => $item['tax_rate_id'] ?? null,
                    'tax_rate' => $taxRate,
                    'supplier_id' => $item['supplier_id'] ?? null,
                    'cost_price' => $item['cost_price'] ?? null,
                    'line_subtotal' => $lineSubtotal,
                    'line_tax' => $lineTax,
                    'line_total' => $lineTotal,
                ]);

                $subtotal += $lineSubtotal;
                $taxTotal += $lineTax;
                $total += $lineTotal;
            }

            $proposal->update([
                'subtotal' => round($subtotal, 2),
                'tax_total' => round($taxTotal, 2),
                'total' => round($total, 2),
            ]);

            return $proposal->fresh(['entity', 'items']);
        });
    }
}
