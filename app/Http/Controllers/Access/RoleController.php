<?php

namespace App\Http\Controllers\Access;

use App\Http\Controllers\Controller;
use App\Http\Requests\Access\StoreRoleRequest;
use App\Http\Requests\Access\UpdateRoleRequest;
use Inertia\Inertia;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    public function index()
    {
        return Inertia::render('Access/Roles/Index', [
            'roles' => Role::query()->orderBy('name')->paginate(20),
        ]);
    }

    public function create()
    {
        return Inertia::render('Access/Roles/Create', [
            'permissionGroups' => $this->permissionGroups(),
        ]);
    }

    public function store(StoreRoleRequest $request)
    {
        $data = $request->validated();

        $role = Role::create(['name' => $data['name']]);
        $role->syncPermissions($data['permissions'] ?? []);

        return redirect()->route('access.roles.index');
    }

    public function edit(Role $role)
    {
        return Inertia::render('Access/Roles/Edit', [
            'role' => $role->only(['id', 'name']),
            'rolePermissions' => $role->permissions()->pluck('name'),
            'permissionGroups' => $this->permissionGroups(),
        ]);
    }

    public function update(UpdateRoleRequest $request, Role $role)
    {
        $data = $request->validated();

        $role->update(['name' => $data['name']]);
        $role->syncPermissions($data['permissions'] ?? []);

        return redirect()->route('access.roles.index');
    }

    private function permissionGroups(): array
    {
        // Agrupar por prefixo (antes do primeiro ponto): entities.*, proposals.*, etc.
        $perms = Permission::query()->orderBy('name')->pluck('name')->all();

        $groups = [];
        foreach ($perms as $name) {
            $prefix = explode('.', $name, 2)[0] ?? 'other';
            $groups[$prefix][] = $name;
        }

        ksort($groups);

        return $groups;
    }
}
