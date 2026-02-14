<?php

namespace App\Actions\Calendar\Types;

use App\Models\CalendarEventType;

class DeleteCalendarEventTypeAction
{
    public function execute(CalendarEventType $type): void
    {
        $type->delete();
    }
}
