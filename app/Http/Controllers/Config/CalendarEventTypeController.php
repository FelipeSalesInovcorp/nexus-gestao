<?php

namespace App\Http\Controllers\Config;

use App\Actions\Calendar\Types\ListCalendarEventTypesAction;
use App\Actions\Calendar\Types\UpsertCalendarEventTypeAction;
use App\Actions\Calendar\Types\DeleteCalendarEventTypeAction;
use App\Http\Controllers\Controller;
use App\Models\CalendarEventType;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CalendarEventTypeController extends Controller
{
    public function index(Request $request, ListCalendarEventTypesAction $action)
    {
        $search = (string) $request->get('search', '');

        return Inertia::render('Config/Calendar/Types/Index', [
            'search' => $search,
            'types' => $action->execute($search),
        ]);
    }

    public function store(Request $request, UpsertCalendarEventTypeAction $action)
    {
        $data = $request->validate([
            'name' => ['required','string','max:120'],
            'color' => ['nullable','string','max:20'],
            'active' => ['required','boolean'],
        ]);

        $action->execute(type: null, data: $data);

        return back()->with('success', 'Tipo criado com sucesso.');
    }

    public function update(Request $request, CalendarEventType $type, UpsertCalendarEventTypeAction $action)
    {
        $data = $request->validate([
            'name' => ['required','string','max:120'],
            'color' => ['nullable','string','max:20'],
            'active' => ['required','boolean'],
        ]);

        $action->execute(type: $type, data: $data);

        return back()->with('success', 'Tipo atualizado com sucesso.');
    }

    public function destroy(CalendarEventType $type, DeleteCalendarEventTypeAction $action)
    {
        $action->execute($type);

        return back()->with('success', 'Tipo removido.');
    }
}
