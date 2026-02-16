<?php

namespace App\Http\Controllers\Access;

use App\Http\Controllers\Controller;
use App\Http\Requests\Access\StoreUserRequest;
use App\Http\Requests\Access\UpdateUserRequest;
use App\Support\EnsureTenantLimits;
use App\Support\TenantContext;
use App\Models\User;
use Inertia\Inertia;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function index()
    {
        return Inertia::render('Access/Users/Index', [
            'users' => User::query()->latest()->paginate(20),
        ]);
    }

    public function create()
    {
        return Inertia::render('Access/Users/Create', [
            'roles' => Role::query()->orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function store(StoreUserRequest $request)
    {

        $data = $request->validated();

        $tenant = TenantContext::get();

        // Enforcement ANTES de criar/anexar user
        if ($tenant) {
            EnsureTenantLimits::canAddUserOrFail($tenant);
        }

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => bcrypt($data['password']),
        ]);

        $user->syncRoles($data['roles'] ?? []);

        if ($tenant) {
            // garante membership na pivot (sem duplicar)
            $tenant->users()->syncWithoutDetaching([
                $user->id => [
                    'role' => 'member',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
            ]);

            // define tenant ativo SEMPRE com o tenant atual (consistência)
            if ($user->active_tenant_id !== $tenant->id) {
                $user->forceFill(['active_tenant_id' => $tenant->id])->save();
            }
        }
    }

    public function edit(User $user)
    {
        return Inertia::render('Access/Users/Edit', [
            'user' => $user->only(['id', 'name', 'email']),
            'roles' => Role::query()->orderBy('name')->get(['id', 'name']),
            'userRoles' => $user->roles()->pluck('name'),
        ]);
    }
    

    public function update(UpdateUserRequest $request, User $user)
    {
        $data = $request->validated();

        $user->update([
            'name' => $data['name'],
            'email' => $data['email'],
        ]);

        if (!empty($data['password'])) {
            $user->update(['password' => bcrypt($data['password'])]);
        }

        $user->syncRoles($data['roles'] ?? []);

        return redirect()->route('access.users.index');
    }
}

