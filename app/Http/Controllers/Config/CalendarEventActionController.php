<?php

namespace App\Http\Controllers\Config;

use App\Actions\Calendar\Actions\DeleteCalendarEventActionAction;
use App\Actions\Calendar\Actions\ListCalendarEventActionsAction;
use App\Actions\Calendar\Actions\UpsertCalendarEventActionAction;
use App\Http\Controllers\Controller;
use App\Models\CalendarEventAction;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CalendarEventActionController extends Controller
{
    public function index(Request $request, ListCalendarEventActionsAction $action)
    {
        $search = (string) $request->get('search', '');

        return Inertia::render('Config/Calendar/Actions/Index', [
            'search' => $search,
            'actions' => $action->execute($search),
        ]);
    }

    public function store(Request $request, UpsertCalendarEventActionAction $action)
    {
        $data = $request->validate([
            'name' => ['required','string','max:120'],
            'active' => ['required','boolean'],
        ]);

        $action->execute(actionModel: null, data: $data);

        return back()->with('success', 'Ação criada com sucesso.');
    }

    public function update(Request $request, CalendarEventAction $actionModel, UpsertCalendarEventActionAction $action)
    {
        $data = $request->validate([
            'name' => ['required','string','max:120'],
            'active' => ['required','boolean'],
        ]);

        $action->execute(actionModel: $actionModel, data: $data);

        return back()->with('success', 'Ação atualizada com sucesso.');
    }

    public function destroy(CalendarEventAction $actionModel, DeleteCalendarEventActionAction $action)
    {
        $action->execute($actionModel);

        return back()->with('success', 'Ação removida.');
    }
}
