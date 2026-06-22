<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        $recursos = [
            'tasks',
            'noticias',
            'users',
            'oportunidades',
            'clientes',
            'paymentrequests',
            'actividades',
            'roles',
            'permisos',
            'user_permissions',
            'areas',
            'tipo_tareas',
            'tipo_cierres',
        ];

        $acciones = ['ver', 'editar', 'eliminar'];

        foreach ($recursos as $recurso) {
            foreach ($acciones as $accion) {
                Permission::firstOrCreate([
                    'recurso' => $recurso,
                    'accion' => $accion,
                ]);
            }
        }

        $admin = Role::firstOrCreate(
            ['nombre' => 'admin'],
            ['descripcion' => 'Administrador con todos los permisos']
        );

        $editor = Role::firstOrCreate(
            ['nombre' => 'editor'],
            ['descripcion' => 'Editor puede ver y editar recursos']
        );

        $viewer = Role::firstOrCreate(
            ['nombre' => 'viewer'],
            ['descripcion' => 'Viewer solo puede ver recursos']
        );

        $admin->permissions()->sync(Permission::all());
        $editor->permissions()->sync(Permission::where('accion', '!=', 'eliminar')->get());
        $viewer->permissions()->sync(Permission::where('accion', 'ver')->get());
    }
}
