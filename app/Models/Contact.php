<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contact extends Model
{
    use HasFactory;

    protected $fillable = [
        'entity_id',
        'contact_role_id',
        'name',
        'email',
        'phone',
        'role',        // podes manter por compatibilidade
        'contact_role_id',
        'is_primary',
    ];

    public function entity()
    {
        return $this->belongsTo(Entity::class);
    }

    public function contactRole()
    {
        return $this->belongsTo(ContactRole::class);
    }
}
