<?php

namespace App\Actions\Calendar\Actions;

use App\Models\CalendarEventAction;

class DeleteCalendarEventActionAction
{
    public function execute(CalendarEventAction $actionModel): void
    {
        $actionModel->delete();
    }
}
