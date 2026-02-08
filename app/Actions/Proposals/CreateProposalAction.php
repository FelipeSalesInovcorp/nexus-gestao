<?php

namespace App\Actions\Proposals;

use App\Models\Proposal;
use Illuminate\Support\Str;

class CreateProposalAction
{
    public function execute(array $data): Proposal
    {
        // número simples (vendável) — depois podemos melhorar
        $nextId = (Proposal::max('id') ?? 0) + 1;
        $number = 'PROP-' . str_pad((string)$nextId, 4, '0', STR_PAD_LEFT);

        return Proposal::create([
            'number' => $number,
            'proposal_date' => $data['proposal_date'],
            'valid_until' => $data['valid_until'],
            'entity_id' => $data['entity_id'],
            'status' => $data['status'] ?? 'draft',
            'notes' => $data['notes'] ?? null,
            'subtotal' => 0,
            'tax_total' => 0,
            'total' => 0,
        ]);
    }
}
