<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\TaxRate;

class Product extends Model
{
    protected $fillable = [
        'sku',
        'reference',
        'name',
        'description',
        'price',
        'tax_rate_id',
        'active',
    ];

    public function taxRate()
    {
        return $this->belongsTo(TaxRate::class);
    }
}
