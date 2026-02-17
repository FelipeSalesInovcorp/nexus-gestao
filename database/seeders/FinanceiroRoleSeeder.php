<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class FinanceiroRoleSeeder extends Seeder
{
    public function run(): void
    {
        $role = Role::firstOrCreate(['name' => 'Financeiro']);

        // coloca aqui só o “mínimo” para o utilizador conseguir usar o sistema
        $perms = [
            'dashboard.view',

            'entities.view',
            'contacts.view',

            'proposals.view',
            'orders.view',

            'supplier-orders.view',
            'supplier-invoices.view',

            'calendar.view',
        ];

        foreach ($perms as $p) {
            Permission::firstOrCreate(['name' => $p]);
        }

        $role->syncPermissions($perms);
    }
}
