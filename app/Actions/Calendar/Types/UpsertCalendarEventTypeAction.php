<?php

namespace App\Actions\Calendar\Types;

use App\Models\CalendarEventType;

class UpsertCalendarEventTypeAction
{
    public function execute(?CalendarEventType $type, array $data): CalendarEventType
    {
        $type ??= new CalendarEventType();

        $type->fill([
            'name' => $data['name'],
            'color' => $data['color'] ?? null,
            'active' => (bool) $data['active'],
        ]);

        $type->save();

        return $type;
    }
}
