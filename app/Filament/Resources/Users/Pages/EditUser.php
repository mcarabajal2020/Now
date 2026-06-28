<?php

namespace App\Filament\Resources\Users\Pages;

use App\Filament\Resources\Users\UserResource;
use App\Models\UserPermission;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    protected function getSaveFormAction(): Action
    {
        return parent::getSaveFormAction()->label('Grabar');
    }

    protected function getCancelFormAction(): Action
    {
        return parent::getCancelFormAction()->label('Cancelar');
    }

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()->label('Eliminar'),
        ];
    }

    protected function afterSave(): void
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
