<?php

namespace App\Actions\Calendar;

use App\Models\CalendarEvent;
use Carbon\Carbon;

class CreateCalendarEventAction
{
    public function execute(array $data): CalendarEvent
    {
        $startAt = Carbon::parse($data['date'] . ' ' . $data['time']);

        return CalendarEvent::create([
            'user_id' => $data['user_id'],
            'entity_id' => $data['entity_id'] ?? null,
            'type_id' => $data['type_id'] ?? null,
            'action_id' => $data['action_id'] ?? null,
            'start_at' => $startAt,
            'duration' => $data['duration'] ?? null,
            'shared' => (bool) ($data['shared'] ?? false),
            'acknowledged' => (bool) ($data['acknowledged'] ?? false),
            'status' => $data['status'],
            'description' => $data['description'] ?? null,
        ]);
    }
}
