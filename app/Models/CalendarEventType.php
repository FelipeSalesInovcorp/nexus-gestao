<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class CalendarEventType extends Model
{
    use SoftDeletes, LogsActivity;

    protected $table = 'calendar_event_types';

    protected $fillable = [
        'name',
        'color',
        'active',
    ];

    protected $casts = [
        'active' => 'boolean',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()
            ->logOnlyDirty()
            ->useLogName('Config Calendário - Tipos');
    }
}
