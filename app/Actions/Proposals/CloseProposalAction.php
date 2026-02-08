<?php

namespace App\Actions\Proposals;

use App\Models\Proposal;
use Carbon\Carbon;

class CloseProposalAction
{
    public function execute(Proposal $proposal): void
    {
        $proposal->update([
            'status' => 'closed',
            'proposal_date' => Carbon::today(),
            'valid_until' => $proposal->proposal_date
                ? $proposal->proposal_date->copy()->addDays(30)
                : Carbon::today()->addDays(30),
        ]);
    }
}
