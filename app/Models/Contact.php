<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use Illuminate\Database\Eloquent\SoftDeletes;


class Contact extends Model
{
    use HasFactory, SoftDeletes, LogsActivity;

    protected $fillable = [
        'number',
        'entity_id',
        'contact_role_id',
        'name',
        'surname',
        'email',
        'phone',
        'mobile',
        'role',        // podes manter por compatibilidade
        'is_primary',
        'rgpd_consent',
        'notes',
        'active',
    ];

    protected $casts = [
        'is_primary' => 'boolean',
        'rgpd_consent' => 'boolean',
        'active' => 'boolean',
    ];

    public function entity()
    {
        return $this->belongsTo(Entity::class);
    }

    public function contactRole()
    {
        return $this->belongsTo(ContactRole::class);
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()
            ->logOnlyDirty()
            ->useLogName('Contactos');
    }

}
