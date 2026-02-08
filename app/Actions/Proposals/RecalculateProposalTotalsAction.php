<?php

namespace App\Actions\Proposals;

use App\Models\Proposal;

class RecalculateProposalTotalsAction
{
    public function execute(Proposal $proposal): void
    {
        $proposal->load('items');

        $subtotal = $proposal->items->sum('line_subtotal');
        $tax = $proposal->items->sum('line_tax');
        $total = $proposal->items->sum('line_total');

        $proposal->update([
            'subtotal' => $subtotal,
            'tax_total' => $tax,
            'total' => $total,
        ]);
    }
}
