<?php

namespace App\Http\Controllers;

use App\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;

class OnboardingController extends Controller
{
    public function index()
    {
        return Inertia::render('Onboarding/Index');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required','string','max:255'],
        ]);

        $user = $request->user();

        $tenant = Tenant::create([
            'owner_user_id' => $user->id,
            'name' => $data['name'],
            'slug' => Str::slug($data['name']) . '-' . uniqid(),
        ]);

        // attach user
        $user->tenants()->attach($tenant->id, ['role' => 'owner']);

        // set active
        $user->update([
            'active_tenant_id' => $tenant->id,
        ]);

        return redirect()->route('dashboard');
    }
}
