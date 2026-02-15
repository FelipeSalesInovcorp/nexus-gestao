<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Entity;
use App\Models\Order;
use App\Models\Concerns\BelongsToTenant;


class SupplierOrder extends Model
{
        use BelongsToTenant;

    protected $fillable = [
        'supplier_id',
        'order_id',
        'number',
        'date',
        'status',
        'total',
    ];

    protected $casts = [
        'date' => 'date',
        'total' => 'decimal:2',
    ];

    public function supplier()
    {
        return $this->belongsTo(Entity::class, 'supplier_id');
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function items()
    {
        return $this->hasMany(\App\Models\SupplierOrderItem::class);
    }
}
