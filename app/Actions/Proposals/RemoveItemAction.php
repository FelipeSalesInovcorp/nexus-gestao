<?php

namespace App\Actions\Proposals;

use App\Models\Proposal;
use App\Models\ProposalItem;

class RemoveItemAction
{
    public function __construct(private RecalculateProposalTotalsAction $recalc) {}

    public function execute(Proposal $proposal, ProposalItem $item): void
    {
        abort_unless($item->proposal_id === $proposal->id, 404);

        $item->delete();
        $this->recalc->execute($proposal);
    }
}
