<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Contact;
use App\Models\Country;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use App\Models\Concerns\BelongsToTenant;


class Entity extends Model
{
    use HasFactory, SoftDeletes, LogsActivity, BelongsToTenant;

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
   
    // Relation with contacts
    /*public function contacts()
    {
        return $this->hasMany(Contact::class);
    }

    // Get primary contact
    /*public function primaryContact()
    //{
        return $this->hasOne(Contact::class)->where('is_primary', true);
    }*/


    public function contacts(): HasMany
    {
        return $this->hasMany(Contact::class);
    }

    public function primaryContact(): HasOne
    {
        return $this->hasOne(Contact::class)->where('is_primary', true);
    }
    
    // Activity Log
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->useLogName('Entidades');
    }
}
