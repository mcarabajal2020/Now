<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
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
            'name' => 'Administrador',
            'email' => 'admin@test.com',
            'password' => bcrypt('password'),
            'role_id' => Role::where('nombre', 'admin')->first()?->id,
            'puede_autorizar' => true,
            'puede_realizar_pago' => true,
            'puede_realizar_transferencia' => true,
        ]);

        $editor = User::create([
            'name' => 'Editor',
            'email' => 'editor@test.com',
            'password' => bcrypt('password'),
            'role_id' => Role::where('nombre', 'editor')->first()?->id,
        ]);

        $viewer = User::create([
            'name' => 'Visualizador',
            'email' => 'viewer@test.com',
            'password' => bcrypt('password'),
            'role_id' => Role::where('nombre', 'viewer')->first()?->id,
        ]);
    }
}
