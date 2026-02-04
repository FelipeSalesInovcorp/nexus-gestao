<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::updateOrCreate(
            ['email' => 'admin@nexus.test'],
            [
                'name' => 'Admin',
                'password' => Hash::make('Admin@12345'),
            ]
        );

        $role = Role::firstOrCreate(['name' => 'admin']);
        $admin->assignRole($role);
    }
}

