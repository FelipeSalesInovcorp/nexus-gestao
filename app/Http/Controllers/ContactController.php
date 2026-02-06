<?php

namespace App\Http\Controllers;

use App\Actions\Contacts\ListContactsAction;
use App\Models\ContactRole;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ContactController extends Controller
{
    public function index(Request $request, ListContactsAction $action)
    {
        return Inertia::render('Contacts/Index', [
            'filters' => [
                'search' => $request->query('search'),
                'role_id' => $request->query('role_id'),
                'primary' => $request->boolean('primary'),
            ],
            'contacts' => $action->execute($request),
            'roles' => ContactRole::orderBy('name')->get(['id', 'name']),
        ]);
    }
}
