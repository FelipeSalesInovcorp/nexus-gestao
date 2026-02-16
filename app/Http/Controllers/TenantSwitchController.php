<?php

namespace App\Http\Controllers;

use App\Actions\Tenants\SwitchTenantAction;
use Illuminate\Http\Request;

class TenantSwitchController extends Controller
{
    public function store(Request $request, SwitchTenantAction $action)
    {
        $data = $request->validate([
            'tenant_id' => ['required', 'integer'],
        ]);

        $action->execute($request->user(), (int) $data['tenant_id']);

        return back();
    }
}
