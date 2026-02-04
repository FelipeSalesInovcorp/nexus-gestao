<?php

namespace App\Http\Controllers;

use App\Actions\Entities\CreateEntityAction;
use App\Actions\Entities\DeleteEntityAction;
use App\Actions\Entities\UpdateEntityAction;
use App\Http\Requests\StoreEntityRequest;
use App\Http\Requests\UpdateEntityRequest;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
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
            new Middleware('permission:entities.create', only: ['store']),
            new Middleware('permission:entities.edit', only: ['update']),
            new Middleware('permission:entities.delete', only: ['destroy']),
        ];
    }
    // ...

    public function index(Request $request)
    {
        $type = $request->query('type'); // client | supplier | null
        $search = $request->query('search');

        $query = Entity::query();

        if ($type === 'client') $query->where('is_client', true);
        if ($type === 'supplier') $query->where('is_supplier', true);

        //if ($search = $request->query('search')) 
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('nif', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        return Inertia::render('Entities/Index', [
            'type' => $type,
            'filters' => [
                'search' => $search,
            ],
            'entities' => $query->orderByDesc('id')->paginate(15)->withQueryString(),
            'countries' => Country::orderBy('name')->get(['id', 'name']),
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
