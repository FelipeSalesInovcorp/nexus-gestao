<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CompanySetting extends Model
{
    protected $table = 'company_settings';

    protected $fillable = [
        'logo_path',
        'name',
        'address',
        'postal_code',
        'locality',
        'tax_number',
    ];

    protected $casts = [
        // O documento pede cifragem na BD; aqui ciframos a morada.
        'address' => 'encrypted',
    ];
}
