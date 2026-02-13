<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class SupplierInvoice extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'supplier_id',
        'supplier_order_id',
        'number',
        'issue_date',
        'due_date',
        'total',
        'status',
        'paid_at',
        'document_path',
        'proof_path',
        'notes',
    ];

    protected $casts = [
        'issue_date' => 'date',
        'due_date'   => 'date',
        'paid_at'    => 'datetime',
        'total'      => 'decimal:2',
    ];

    public function supplier()
    {
        return $this->belongsTo(Entity::class, 'supplier_id');
    }

    public function supplierOrder()
    {
        return $this->belongsTo(SupplierOrder::class);
    }
    
    // Activity Log
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()
            ->logOnlyDirty()
            ->useLogName('Faturas Fornecedor');
    }
}
