<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use App\Models\Product;

class SupplierOrderItem extends Model
{
    protected $fillable = [
        'supplier_order_id',
        'product_id',
        'description',
        'quantity',
        'cost_price',
    ];

    protected $casts = [
        'cost_price' => 'decimal:2',
    ];

    public function supplierOrder()
    {
        return $this->belongsTo(SupplierOrder::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
