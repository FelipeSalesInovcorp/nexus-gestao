<?php

namespace App\Actions\Calendar;

use App\Models\CalendarEvent;
use Illuminate\Http\Request;

class ListCalendarEventsAction
{
    public function execute(Request $request)
    {
        $q = CalendarEvent::query()
            ->with(['type', 'entity', 'user'])
            ->latest('start_at');

        if ($request->filled('start')) {
            $q->where('start_at', '>=', $request->date('start'));
        }

        if ($request->filled('end')) {
            $q->where('start_at', '<', $request->date('end'));
        }

        if ($request->filled('user_id')) {
            $q->where('user_id', (int) $request->user_id);
        }

        if ($request->filled('entity_id')) {
            $q->where('entity_id', (int) $request->entity_id);
        }

        return $q->get()->map(function ($e) {
            $end = $e->duration ? $e->start_at->copy()->addMinutes($e->duration) : null;

            return [
                'id' => $e->id,
                'title' => $e->type?->name ?? 'Atividade',
                'start' => $e->start_at->toIso8601String(),
                'end' => $end?->toIso8601String(),
                'color' => $e->type?->color,
                'extendedProps' => [
                    'user_id' => $e->user_id,
                    'entity_id' => $e->entity_id,
                    'type_id' => $e->type_id,
                    'action_id' => $e->action_id,
                    'duration' => $e->duration,
                    'shared' => $e->shared,
                    'acknowledged' => $e->acknowledged,
                    'status' => $e->status,
                    'description' => $e->description,
                ],
            ];
        });
    }
}
