<?php

namespace App\Actions\Contacts;

use App\Models\Contact;
use Illuminate\Http\Request;

class ListContactsAction
{
    public function execute(Request $request)
    {
        $search = $request->query('search');
        $roleId = $request->query('role_id');
        $onlyPrimary = $request->boolean('primary');

        $query = Contact::query()
            ->with([
                'entity:id,number,name',
                'contactRole:id,name',
            ]);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        if ($roleId) {
            $query->where('contact_role_id', $roleId);
        }

        if ($onlyPrimary) {
            $query->where('is_primary', true);
        }

        return $query
            ->orderByDesc('id')
            ->paginate(15)
            ->withQueryString()
            ->through(function ($c) {
                return [
                    'id' => $c->id,
                    'name' => $c->name,
                    'email' => $c->email,
                    'phone' => $c->phone,
                    'is_primary' => (bool) $c->is_primary,
                    'role_name' => $c->contactRole?->name ?? $c->role,
                    'entity' => $c->entity ? [
                        'id' => $c->entity->id,
                        'number' => $c->entity->number,
                        'name' => $c->entity->name,
                    ] : null,
                ];
            });
    }
}
