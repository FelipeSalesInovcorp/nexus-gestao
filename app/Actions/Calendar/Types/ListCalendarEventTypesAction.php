<?php

namespace App\Actions\Calendar\Types;

use App\Models\CalendarEventType;

class ListCalendarEventTypesAction
{
    public function execute(string $search = '')
    {
        return CalendarEventType::query()
            ->when($search, fn ($q) => $q->where('name', 'like', "%{$search}%"))
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString()
            ->through(fn ($t) => [
                'id' => $t->id,
                'name' => $t->name,
                'color' => $t->color,
                'active' => (bool) $t->active,
                'created_at' => optional($t->created_at)->format('Y-m-d'),
            ]);
    }
}
