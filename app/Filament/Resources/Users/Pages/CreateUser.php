<?php

namespace App\Filament\Resources\Users\Pages;

use App\Filament\Resources\Users\UserResource;
use App\Models\UserPermission;
use Filament\Resources\Pages\CreateRecord;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;

    protected function afterCreate(): void
    {
        $this->syncPermissions($this->record->id);
    }

    private function syncPermissions(int $userId): void
    {
        $data = $this->data;

        UserPermission::where('user_id', $userId)->delete();

        foreach ($data['permisos_ver'] ?? [] as $recurso) {
            UserPermission::create([
                'user_id' => $userId,
                'recurso' => $recurso,
                'accion' => 'ver',
            ]);
        }

        foreach ($data['permisos_editar'] ?? [] as $recurso) {
            UserPermission::create([
                'user_id' => $userId,
                'recurso' => $recurso,
                'accion' => 'editar',
            ]);
        }

        foreach ($data['permisos_oculto'] ?? [] as $recurso) {
            UserPermission::create([
                'user_id' => $userId,
                'recurso' => $recurso,
                'accion' => 'oculto',
            ]);
        }
    }
}
