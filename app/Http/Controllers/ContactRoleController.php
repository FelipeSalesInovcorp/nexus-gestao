<?php

namespace App\Http\Controllers;

use App\Actions\ContactRoles\CreateContactRoleAction;
use App\Actions\ContactRoles\DeleteContactRoleAction;
use App\Actions\ContactRoles\UpdateContactRoleAction;
use App\Http\Requests\StoreContactRoleRequest;
use App\Http\Requests\UpdateContactRoleRequest;
use App\Models\ContactRole;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ContactRoleController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->query('search');

        $query = ContactRole::query();

        if ($search) {
            $query->where('name', 'like', "%{$search}%");
        }

        return Inertia::render('Config/ContactRoles/Index', [
            'filters' => ['search' => $search],
            'roles' => $query->orderBy('name')->paginate(15)->withQueryString(),
        ]);
    }

    public function create()
    {
        return Inertia::render('Config/ContactRoles/Create');
    }

    public function store(StoreContactRoleRequest $request, CreateContactRoleAction $action)
    {
        $action->execute($request->validated());
        return redirect()->route('contact-roles.index');
    }

    public function edit(ContactRole $role)
    {
        return Inertia::render('Config/ContactRoles/Edit', [
            'role' => $role,
        ]);
    }

    public function update(UpdateContactRoleRequest $request, ContactRole $role, UpdateContactRoleAction $action)
    {
        $action->execute($role, $request->validated());
        return redirect()->route('contact-roles.index');
    }

    public function destroy(ContactRole $role, DeleteContactRoleAction $action)
    {
        $action->execute($role);
        return redirect()->back();
    }
}
