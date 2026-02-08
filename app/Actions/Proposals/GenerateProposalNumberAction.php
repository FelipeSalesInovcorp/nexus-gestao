<?php

namespace App\Actions\Proposals;

use App\Models\Proposal;

class GenerateProposalNumberAction
{
    public function execute(): string
    {
        $last = Proposal::max('id') ?? 0;
        return 'PROP-' . str_pad((string)($last + 1), 4, '0', STR_PAD_LEFT);
    }
}
