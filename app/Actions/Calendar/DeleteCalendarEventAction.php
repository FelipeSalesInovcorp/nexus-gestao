<?php

namespace App\Actions\Calendar;

use App\Models\CalendarEvent;

class DeleteCalendarEventAction
{
    public function execute(CalendarEvent $event): void
    {
        $event->delete();
    }
}
