<?php

namespace App\Http\Controllers;

use App\Actions\Calendar\CreateCalendarEventAction;
use App\Actions\Calendar\DeleteCalendarEventAction;
use App\Actions\Calendar\ListCalendarEventsAction;
use App\Actions\Calendar\UpdateCalendarEventAction;
use App\Models\CalendarEvent;
use App\Models\CalendarEventAction;
use App\Models\CalendarEventType;
use App\Models\Entity;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class CalendarController extends Controller
{
    public function index()
    {
        return Inertia::render('Calendar/Index', [
            'users' => User::query()->orderBy('name')->get(['id','name']),
            'entities' => Entity::query()->orderBy('name')->get(['id','name']),
            'types' => CalendarEventType::query()->where('active', true)->orderBy('name')->get(['id','name','color']),
            'actions' => CalendarEventAction::query()->where('active', true)->orderBy('name')->get(['id','name']),
            'statusOptions' => ['scheduled','done','canceled'],
        ]);
    }

    public function events(Request $request, ListCalendarEventsAction $action)
    {
        $request->validate([
            'start' => ['nullable','date'],
            'end' => ['nullable','date'],
            'user_id' => ['nullable','integer'],
            'entity_id' => ['nullable','integer'],
        ]);

        return $action->execute($request);
    }

    public function store(Request $request, CreateCalendarEventAction $action)
    {
        $data = $this->validateEvent($request);

        $action->execute($data);

        return response()->json(['ok' => true]);
    }

    public function update(Request $request, CalendarEvent $event, UpdateCalendarEventAction $action)
    {
        $data = $this->validateEvent($request);

        $action->execute($event, $data);

        return response()->json(['ok' => true]);
    }

    public function destroy(CalendarEvent $event, DeleteCalendarEventAction $action)
    {
        $action->execute($event);

        return response()->json(['ok' => true]);
    }

    private function validateEvent(Request $request): array
    {
        return $request->validate([
            'user_id' => ['required','integer', Rule::exists('users','id')],
            'entity_id' => ['nullable','integer', Rule::exists('entities','id')],
            'type_id' => ['nullable','integer', Rule::exists('calendar_event_types','id')],
            'action_id' => ['nullable','integer', Rule::exists('calendar_event_actions','id')],

            'date' => ['required','date'],
            'time' => ['required','date_format:H:i'],
            'duration' => ['nullable','integer','min:1','max:1440'],

            'shared' => ['boolean'],
            'acknowledged' => ['boolean'],

            'status' => ['required', Rule::in(['scheduled','done','canceled'])],
            'description' => ['nullable','string','max:5000'],
        ]);
    }
}
