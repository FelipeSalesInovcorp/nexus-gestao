<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class Contact extends Model
{
    //

    use HasFactory;

    protected $fillable = [
        'entity_id',
        'name',
        'email',
        'phone',
        'role',
        'is_primary',
    ];

    public function entity()
    {
        return $this->belongsTo(Entity::class);
    }

}
