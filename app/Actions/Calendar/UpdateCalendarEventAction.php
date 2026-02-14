<?php

namespace App\Actions\Calendar;

use App\Models\CalendarEvent;
use App\Models\CalendarEventAction;
use Carbon\Carbon;

class UpdateCalendarEventAction
{
    public function execute(CalendarEvent $event, array $data): CalendarEvent
    {
        $startAt = Carbon::parse($data['date'] . ' ' . $data['time']);

        $event->update([
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

        return $event;
    }
}
