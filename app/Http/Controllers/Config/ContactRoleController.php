<?php

namespace App\Http\Controllers\Config;

use App\Http\Controllers\Controller;
use App\Models\ContactRole;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ContactRoleController extends Controller
{
    public function index()
    {
        return Inertia::render('Config/ContactRoles/Index', [
            'roles' => ContactRole::orderBy('name')->paginate(10),
        ]);
    }

    public function create()
    {
        return Inertia::render('Config/ContactRoles/Create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:120|unique:contact_roles,name',
        ]);

        ContactRole::create([
            'name' => $request->name,
            'active' => true,
        ]);

        return redirect()->route('contact-roles.index');
    }

    public function edit(ContactRole $contactRole)
    {
        return Inertia::render('Config/ContactRoles/Edit', [
            'role' => $contactRole,
        ]);
    }

    public function update(Request $request, ContactRole $contactRole)
    {
        $request->validate([
            'name' => 'required|string|max:120|unique:contact_roles,name,' . $contactRole->id,
        ]);

        $contactRole->update($request->only('name', 'active'));

        return redirect()->route('contact-roles.index');
    }

    public function destroy(ContactRole $contactRole)
    {
        $contactRole->delete();

        return back();
    }
}

