<?php

namespace App\Actions\Calendar\Actions;

use App\Models\CalendarEventAction;

class ListCalendarEventActionsAction
{
    public function execute(string $search = '')
    {
        return CalendarEventAction::query()
            ->when($search, fn ($q) => $q->where('name', 'like', "%{$search}%"))
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString()
            ->through(fn ($a) => [
                'id' => $a->id,
                'name' => $a->name,
                'active' => (bool) $a->active,
                'created_at' => optional($a->created_at)->format('Y-m-d'),
            ]);
    }
}
