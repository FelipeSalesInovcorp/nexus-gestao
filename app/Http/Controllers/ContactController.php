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
        $filters = [
            'search' => $request->query('search'),
            'contact_role_id' => $request->query('contact_role_id'),
            'only_primary' => $request->boolean('only_primary'),
        ];

        $contacts = $action->execute($filters);

        return Inertia::render('Contacts/Index', [
            'filters' => $filters,
            'contacts' => $contacts,
            'roles' => ContactRole::orderBy('name')->get(['id', 'name']),
        ]);
    }
}
