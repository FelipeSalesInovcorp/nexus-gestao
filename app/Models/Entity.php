<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Entity extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'number',
        'is_client',
        'is_supplier',
        'name',
        'nif',
        'address',
        'postal_code',
        'city',
        'country_id',
        'email',
        'phone',
        'mobile',
        'website',
        'notes',
        'rgpd_consent',
        'active',
    ];

    protected $casts = [
        'address' => 'encrypted',
        'notes' => 'encrypted',
        'is_client' => 'boolean',
        'is_supplier' => 'boolean',
        'active' => 'boolean',
        'rgpd_consent' => 'boolean',
    ];

    public function country()
    {
        return $this->belongsTo(Country::class);
    }
}
