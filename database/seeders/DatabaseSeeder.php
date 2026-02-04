<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Executa o seeder de países
        $this->call(CountrySeeder::class);
        
        // Executa o seeder de papéis e permissões
        $this->call(RolePermissionSeeder::class);

        $this->call(AdminUserSeeder::class);



        /*User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);*/


    }
}
