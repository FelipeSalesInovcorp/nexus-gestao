<?php

namespace App\Actions\Contacts;

use App\Models\Contact;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ListContactsAction
{
    public function execute(array $filters): LengthAwarePaginator
    {
        $search = $filters['search'] ?? null;
        $roleId = $filters['contact_role_id'] ?? null;
        $onlyPrimary = (bool)($filters['only_primary'] ?? false);

        return Contact::query()
            ->with([
                'entity:id,number,name',
                'contactRole:id,name',
            ])
            ->when($search, function ($q) use ($search) {
                $q->where(function ($qq) use ($search) {
                    $qq->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%");
                });
            })
            ->when($roleId, fn ($q) => $q->where('contact_role_id', $roleId))
            ->when($onlyPrimary, fn ($q) => $q->where('is_primary', true))
            ->orderByDesc('is_primary')
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString()
            ->through(function ($c) {
                return [
                    'id' => $c->id,
                    'name' => $c->name,
                    'email' => $c->email,
                    'phone' => $c->phone,
                    'is_primary' => (bool) $c->is_primary,

                    // “Cargo” (preferir ContactRole; fallback para string antiga)
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
