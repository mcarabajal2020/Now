<?php

namespace App\Filament\Resources\Tasks\Pages;

use App\Filament\Resources\Tasks\TaskResource;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreateTask extends CreateRecord
{
    protected static string $resource = TaskResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $user = auth()->user();

        // (Para IDE) si no hay usuario autenticado, no se permite crear.
        if (! $user) {
            abort(403);
        }

        $data['usuario_solicita_id'] = $user->id;
        $data['fecha_creacion'] = $data['fecha_creacion'] ?? now();

        // Forzar estado/fechas según requisitos.
        // En el formulario de CREATE el campo `estado` está oculto, así que debemos setearlo aquí.
        $data['estado'] = 'Nuevo';
        $data['fecha_finalizacion'] = null;

        return $data;
    }

    protected function afterCreate(): void
    {
        $this->sendAssignedTaskNotification($this->record->asignado_a_id);
    }

    protected function afterPersist(): void
    {
        // Registrar historial de creación
        $this->record->histories()->create([
            'user_id' => auth()->id(),
            'tipo' => 'creado',
            'comentario' => 'Tarea creada por '.(auth()->user()?->name ?? 'Sistema'),
        ]);
    }

    private function sendAssignedTaskNotification(?int $assignedUserId): void
    {
        if (! $assignedUserId) {
            return;
        }

        $assignedUser = User::find($assignedUserId);

        if (! $assignedUser) {
            return;
        }

        $notification = Notification::make()
            ->title('Nueva tarea asignada')
            ->body("Se te asignó la tarea: {$this->record->titulo}")
            ->icon('heroicon-o-clipboard-document-list')
            ->actions([
                Action::make('view')
                    ->label('Ver tarea')
                    ->url(TaskResource::getUrl('edit', ['record' => $this->record]))
                    ->button()
                    ->markAsRead(),
            ]);

        $assignedUser->notifyNow($notification->toDatabase());
    }
}
