<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'number', 'order_date', 'status', 'total',
        'entity_id', 'proposal_id',
    ];

    public function entity()
    {
        return $this->belongsTo(Entity::class);
    }

    public function proposal()
    {
        return $this->belongsTo(Proposal::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }
}
