<?php

namespace App\Actions\Calendar\Actions;

use App\Models\CalendarEventAction;

class UpsertCalendarEventActionAction
{
    public function execute(?CalendarEventAction $actionModel, array $data): CalendarEventAction
    {
        $actionModel ??= new CalendarEventAction();

        $actionModel->fill([
            'name' => $data['name'],
            'active' => (bool) $data['active'],
        ]);

        $actionModel->save();

        return $actionModel;
    }
}
