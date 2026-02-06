<?php

namespace App\Http\Controllers;

use App\Actions\Entities\CreateEntityAction;
use App\Actions\Entities\DeleteEntityAction;
use App\Actions\Entities\UpdateEntityAction;
use App\Http\Requests\StoreEntityRequest;
use App\Http\Requests\UpdateEntityRequest;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use App\Models\ContactRole;
use App\Models\Country;
use App\Models\Entity;
use Illuminate\Http\Request;
use Inertia\Inertia;

    class EntityController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:entities.view', only: ['index']),
            new Middleware('permission:entities.create', only: ['create','store']),
            new Middleware('permission:entities.edit', only: ['edit','update']),
            new Middleware('permission:entities.delete', only: ['destroy']),
        ];
    }
    // ...

    public function index(Request $request)
    {
        $type = $request->query('type'); // client | supplier | null
        $search = $request->query('search');

        $query = Entity::query()
            ->with(['primaryContact.contactRole', 'contacts.contactRole']); //  NOVO eager-load

        if ($type === 'client') {
            $query->where('is_client', true);
        }

        if ($type === 'supplier') {
            $query->where('is_supplier', true);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('nif', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $entities = $query
            ->orderByDesc('id')
            ->paginate(15)
            ->withQueryString()
            ->through(function ($e) {
                return [
                    'id' => $e->id,
                    'number' => $e->number,
                    'name' => $e->name,
                    'nif' => $e->nif,
                    'email' => $e->email,
                    'is_client' => (bool) $e->is_client,
                    'is_supplier' => (bool) $e->is_supplier,
                    'active' => (bool) $e->active,

                    //  NOVO
                    'primary_contact' => $e->primaryContact ? [
                        'name' => $e->primaryContact->name,
                        'role_name' => $e->primaryContact->contactRole?->name
                            ?? $e->primaryContact->role, // fallback
                    ] : null,
                ];
            });

        return Inertia::render('Entities/Index', [
            'type' => $type,
            'filters' => [
                'search' => $search,
            ],
            'entities' => $entities, //  troca 
            'countries' => Country::orderBy('name')->get(['id', 'name']),
        ]);
    }


    //  create()
    public function create()
    {
        return Inertia::render('Entities/Create', [
            'countries' => Country::orderBy('name')->get(['id', 'name']),
        ]);
    }

    //  edit()
    public function edit(Entity $entity)
    {
        /*$entity->load('contacts');

        return Inertia::render('Entities/Edit', [
            'entity' => $entity,
            'countries' => Country::orderBy('name')->get(['id', 'name']),
            'contactRoles' => ContactRole::orderBy('name')->get(['id', 'name']),

        ]);*/

        $entity->load(['contacts.contactRole']);

        return Inertia::render('Entities/Edit', [
            'entity' => $entity,
            'countries' => Country::orderBy('name')->get(['id', 'name']),
            'contactRoles' => \App\Models\ContactRole::orderBy('name')->get(['id', 'name']),
        ]);
    }


    public function store(StoreEntityRequest $request, CreateEntityAction $action)
    {
        $action->execute($request->validated());
        return redirect()->back()->with('success', 'Entidade criada com sucesso.');
    }

    public function update(UpdateEntityRequest $request, Entity $entity, UpdateEntityAction $action)
    {
        $action->execute($entity, $request->validated());
        return redirect()->back()->with('success', 'Entidade atualizada com sucesso.');
    }

    public function destroy(Entity $entity, DeleteEntityAction $action)
    {
        $action->execute($entity);
        return redirect()->back()->with('success', 'Entidade removida com sucesso.');
    }
}
