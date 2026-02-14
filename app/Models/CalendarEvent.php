<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class CalendarEvent extends Model
{
    use SoftDeletes, LogsActivity;

    protected $table = 'calendar_events';

    protected $fillable = [
        'user_id',
        'entity_id',
        'type_id',
        'action_id',
        'start_at',
        'duration',
        'shared',
        'acknowledged',
        'status',
        'description',
    ];

    protected $casts = [
        'start_at' => 'datetime',
        'duration' => 'integer',
        'shared' => 'boolean',
        'acknowledged' => 'boolean',
    ];

    // Relations
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function entity()
    {
        return $this->belongsTo(Entity::class);
    }

    public function type()
    {
        return $this->belongsTo(CalendarEventType::class, 'type_id');
    }

    public function action()
    {
        return $this->belongsTo(CalendarEventAction::class, 'action_id');
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()
            ->logOnlyDirty()
            ->useLogName('Calendário');
    }
}
