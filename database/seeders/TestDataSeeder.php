<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TestDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Crear usuarios de prueba con diferentes roles
        $admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@test.com',
            'password' => bcrypt('password'),
            'role_id' => Role::where('nombre', 'admin')->first()?->id,
        ]);

        $editor = User::create([
            'name' => 'Editor User',
            'email' => 'editor@test.com',
            'password' => bcrypt('password'),
            'role_id' => Role::where('nombre', 'editor')->first()?->id,
        ]);

        $viewer = User::create([
            'name' => 'Viewer User',
            'email' => 'viewer@test.com',
            'password' => bcrypt('password'),
            'role_id' => Role::where('nombre', 'viewer')->first()?->id,
        ]);
    }
}
