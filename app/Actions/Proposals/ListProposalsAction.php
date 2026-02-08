<?php

namespace App\Actions\Proposals;

use App\Models\Proposal;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ListProposalsAction
{

public function execute(array $filters)
{
    return Proposal::query()
        ->with(['entity:id,name'])
        // se tiveres select(), inclui reference:
        ->select([
            'id',
            'number',
            'proposal_date',
            'valid_until',
            'status',
            'total',
            'entity_id',
        ])
        ->when($filters['status'] ?? null, fn ($q, $status) => $q->where('status', $status))
        ->when($filters['search'] ?? null, function ($q, $search) {
            $search = trim($search);
            $q->where(function ($qq) use ($search) {
                $qq->where('number', 'like', "%{$search}%");
            });
        })
        ->orderByDesc('proposal_date')
        ->paginate(10)
        ->through(fn ($p) => [
            'id' => $p->id,
            'number' => $p->number,
            //'proposal_date' => $p->proposal_date,
            'proposal_date' => optional($p->proposal_date)->format('Y-m-d'),
            'valid_until' => optional($p->valid_until)->format('Y-m-d'),
            //'valid_until' => $p->valid_until,
            'status' => $p->status,
            'total' => $p->total,
            'entity' => $p->entity
                ? ['id' => $p->entity->id, 'name' => $p->entity->name]
                : null,
        ]);
}
}