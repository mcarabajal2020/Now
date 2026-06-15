<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Crear permisos
        $permisos = [
            ['recurso' => 'tasks', 'accion' => 'ver'],
            ['recurso' => 'tasks', 'accion' => 'editar'],
            ['recurso' => 'tasks', 'accion' => 'eliminar'],

            ['recurso' => 'noticias', 'accion' => 'ver'],
            ['recurso' => 'noticias', 'accion' => 'editar'],
            ['recurso' => 'noticias', 'accion' => 'eliminar'],

            ['recurso' => 'users', 'accion' => 'ver'],
            ['recurso' => 'users', 'accion' => 'editar'],
            ['recurso' => 'users', 'accion' => 'eliminar'],

            ['recurso' => 'tipo_tareas', 'accion' => 'ver'],
            ['recurso' => 'tipo_tareas', 'accion' => 'editar'],
            ['recurso' => 'tipo_tareas', 'accion' => 'eliminar'],

            ['recurso' => 'tipo_cierres', 'accion' => 'ver'],
            ['recurso' => 'tipo_cierres', 'accion' => 'editar'],
            ['recurso' => 'tipo_cierres', 'accion' => 'eliminar'],
        ];

        foreach ($permisos as $permiso) {
            Permission::firstOrCreate($permiso);
        }

        // Crear roles
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

        // Asignar permisos al rol Admin (todos)
        $adminPermisos = Permission::all();
        $admin->permissions()->sync($adminPermisos);

        // Asignar permisos al rol Editor (ver y editar)
        $editorPermisos = Permission::where('accion', '!=', 'eliminar')->get();
        $editor->permissions()->sync($editorPermisos);

        // Asignar permisos al rol Viewer (solo ver)
        $viewerPermisos = Permission::where('accion', 'ver')->get();
        $viewer->permissions()->sync($viewerPermisos);
    }
}
