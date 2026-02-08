<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProposalItem extends Model
{
    protected $fillable = [
        'proposal_id',
        'product_id',
        'description',
        'qty',
        'unit_price',
        'tax_rate_id',
        'tax_rate',
        'supplier_id',
        'cost_price',
        'line_subtotal',
        'line_tax',
        'line_total',
    ];

    protected $casts = [
        'qty' => 'decimal:2',
        'unit_price' => 'decimal:2',
        'tax_rate' => 'decimal:2',
        'cost_price' => 'decimal:2',
        'line_subtotal' => 'decimal:2',
        'line_tax' => 'decimal:2',
        'line_total' => 'decimal:2',
    ];

    public function proposal(): BelongsTo
    {
        return $this->belongsTo(Proposal::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function taxRate(): BelongsTo
    {
        return $this->belongsTo(TaxRate::class);
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Entity::class, 'supplier_id');
    }
}

