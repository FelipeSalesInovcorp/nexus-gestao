<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Proposal extends Model
{
    protected $fillable = [
        'number',
        'proposal_date',
        'valid_until',
        'entity_id',
        'status',
        'subtotal',
        'tax_total',
        'total',
        'notes',
    ];

    protected $casts = [
        'proposal_date' => 'date',
        'valid_until' => 'date',
        'subtotal' => 'decimal:2',
        'tax_total' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    public function entity(): BelongsTo
    {
        return $this->belongsTo(Entity::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(ProposalItem::class);
    }
}
