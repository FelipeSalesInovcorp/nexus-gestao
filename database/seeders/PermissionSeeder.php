<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            // Dashboard
            'dashboard.view',

            // Entities / Contacts
            'entities.view', 'entities.create', 'entities.update', 'entities.delete',
            'contacts.view', 'contacts.create', 'contacts.update', 'contacts.delete',

            // Config
            'config.contact-roles.view', 'config.contact-roles.create', 'config.contact-roles.update', 'config.contact-roles.delete',
            'config.tax-rates.view', 'config.tax-rates.create', 'config.tax-rates.update', 'config.tax-rates.delete',
            'config.products.view', 'config.products.create', 'config.products.update', 'config.products.delete',
            'config.company.update',

            // Proposals / Orders
            'proposals.view', 'proposals.create', 'proposals.update', 'proposals.delete',
            'orders.view', 'orders.create', 'orders.update', 'orders.delete',
            'supplier-orders.view',

            // Finance
            'supplier-invoices.view', 'supplier-invoices.create', 'supplier-invoices.update',
            'supplier-invoices.mark-paid', 'supplier-invoices.download',

            // Access management
            'access.users.view', 'access.users.create', 'access.users.update', 'access.users.delete',
            'access.roles.view', 'access.roles.create', 'access.roles.update', 'access.roles.delete',
        ];

        foreach ($permissions as $name) {
            Permission::findOrCreate($name);
        }

        // Role Admin com tudo
        $admin = Role::findOrCreate('Admin');
        $admin->syncPermissions(Permission::all());
    }
}
