<?php

namespace App\Filament\Resources\Tasks\Pages;

use App\Filament\Resources\Tasks\TaskResource;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditTask extends EditRecord
{
    protected static string $resource = TaskResource::class;

    private ?int $previousAssignedUserId = null;

    private ?string $previousEstado = null;

    private ?string $newProgressDetail = null;

    protected function getSaveFormAction(): Action
    {
        return parent::getSaveFormAction()->label('Grabar');
    }

    protected function getCancelFormAction(): Action
    {
        return parent::getCancelFormAction()->label('Cancelar');
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $this->previousAssignedUserId = $this->record->asignado_a_id;
        $this->previousEstado = $this->record->estado;

        $this->newProgressDetail = trim((string) ($data['nuevo_detalle'] ?? ''));

        unset($data['nuevo_detalle']);

        if ($this->newProgressDetail !== '' && $this->isAssignedToCurrentUser()) {
            $data['detalle'] = $this->appendProgressDetail($this->record->detalle, $this->newProgressDetail);

            if ($this->record->estado === 'Nuevo') {
                $data['estado'] = 'En Proceso';
            }
        }

        $data['ultima_modificacion'] = now();

        if (($data['estado'] ?? null) === 'Finalizado') {
            $data['fecha_finalizacion'] = $data['fecha_finalizacion'] ?? now();
        } else {
            $data['fecha_finalizacion'] = null;
        }

        // Evitar edición desde UI del solicitante
        $data['usuario_solicita_id'] = $this->record->usuario_solicita_id;

        return $data;
    }

    protected function afterSave(): void
    {
        $this->record->refresh();

        // Enviar notificación si cambió la asignación
        if ($this->record->asignado_a_id !== $this->previousAssignedUserId) {
            $this->record->histories()->create([
                'user_id' => auth()->id(),
                'tipo' => 'asignacion',
                'old_value' => (string) $this->previousAssignedUserId,
                'new_value' => (string) $this->record->asignado_a_id,
                'comentario' => "Asignado a usuario ID {$this->record->asignado_a_id}",
            ]);

            $this->sendAssignedTaskNotification($this->record->asignado_a_id);
        }

        // Registrar cambio de estado
        if ($this->previousEstado !== $this->record->estado) {
            $this->record->histories()->create([
                'user_id' => auth()->id(),
                'tipo' => 'estado',
                'old_value' => (string) $this->previousEstado,
                'new_value' => (string) $this->record->estado,
                'comentario' => "Estado: {$this->previousEstado} -> {$this->record->estado}",
            ]);
        }

        // Registrar nuevo detalle de avance
        if (filled($this->newProgressDetail)) {
            $this->record->histories()->create([
                'user_id' => auth()->id(),
                'tipo' => 'comentario',
                'comentario' => $this->newProgressDetail,
            ]);
        }
    }

    public function finalize(?int $tipoCierreId = null, ?string $comentarioCierre = null): void
    {

        if (! $this->isAssignedToCurrentUser() || $this->record->estado === 'Finalizado') {
            abort(403);
        }

        $this->save(shouldRedirect: false, shouldSendSavedNotification: false);

        $this->record->update([
            'estado' => 'Finalizado',
            'fecha_finalizacion' => now(),
            'ultima_modificacion' => now(),
        ]);

        // Registrar tipo de cierre en el historial
        if ($tipoCierreId) {
            $tipoLabel = $this->record->tipoCierre?->nombre;

            $this->record->histories()->create([
                'user_id' => auth()->id(),
                'tipo' => 'finalizacion',
                'old_value' => (string) $this->record->estado,
                'new_value' => 'Finalizado',
                'comentario' => trim(($tipoLabel ? "Tipo cierre: {$tipoLabel}. " : '').($comentarioCierre ?? '')),
            ]);
        }

        Notification::make()
            ->success()
            ->title('Tarea finalizada')
            ->send();

        $this->redirect($this->getResource()::getUrl('edit', ['record' => $this->record]), navigate: false);
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('take_task')
                ->label('Tomar tarea')
                ->icon('heroicon-o-hand-raised')
                ->color('primary')
                ->visible(fn (): bool => $this->canTakeTask())
                ->action(function (): void {
                    $this->record->update([
                        'asignado_a_id' => auth()->id(),
                        'ultima_modificacion' => now(),
                        'estado' => $this->record->estado === 'Nuevo' ? 'En Proceso' : $this->record->estado,
                    ]);

                    $this->record->histories()->create([
                        'user_id' => auth()->id(),
                        'tipo' => 'asignacion',
                        'old_value' => (string) $this->previousAssignedUserId,
                        'new_value' => (string) $this->record->asignado_a_id,
                        'comentario' => 'Tarea tomada por el usuario logueado',
                    ]);

                    $this->sendAssignedTaskNotification($this->record->asignado_a_id);

                    $this->record->refresh();
                }),

            DeleteAction::make()->label('Eliminar'),
        ];
    }

    private function canTakeTask(): bool
    {
        $user = auth()->user();

        if (! $user || ! $this->record) {
            return false;
        }

        if ($this->isAssignedToCurrentUser()) {
            return false;
        }

        if ($this->record->estado === 'Finalizado') {
            return false;
        }

        if (! $this->record->area_id || ! $user->areas->contains('id', $this->record->area_id)) {
            return false;
        }

        return true;
    }

    protected function getFormActions(): array
    {
        return [
            $this->getSaveFormAction(),
            $this->getFinalizeFormAction(),
            $this->getCancelFormAction(),
        ];
    }

    protected function getFinalizeFormAction(): Action
    {
        return Action::make('finalize')
            ->label('Finalizar')
            ->color('success')
            ->modalHeading('Finalizar tarea')
            ->modalDescription('La tarea pasará a Finalizado. Seleccioná el tipo de cierre y opcionalmente dejá un comentario.')
            ->modalSubmitActionLabel('Finalizar')
            ->form([
                Select::make('tipo_cierre_id')
                    ->label('Tipo de cierre')
                    ->relationship('tipoCierre', 'nombre')
                    ->searchable()
                    ->preload()
                    ->required(),

                Textarea::make('comentario')
                    ->label('Comentario (opcional)')
                    ->rows(3)
                    ->maxLength(2000),
            ])
            ->action(fn (array $data): null => $this->finalize($data['tipo_cierre_id'] ?? null, $data['comentario'] ?? null))

            ->visible(fn (): bool => $this->isAssignedToCurrentUser() && $this->record->estado !== 'Finalizado');
    }

    private function isAssignedToCurrentUser(): bool
    {
        $currentUser = auth()->user();

        if (! $currentUser) {
            return false;
        }

        // Permitir al usuario asignado
        if ($this->record->asignado_a_id === $currentUser->id) {
            return true;
        }

        // Permitir a cualquier usuario de la misma área
        if (! is_null($this->record->area_id) && $currentUser->areas->contains('id', $this->record->area_id)) {
            return true;
        }

        return false;
    }

    private function appendProgressDetail(?string $currentDetail, string $newProgressDetail): string
    {
        $userName = auth()->user()?->name ?? 'Usuario desconocido';
        $timestamp = now()->format('d/m/Y H:i');
        $entry = "[{$timestamp}] {$userName}: {$newProgressDetail}";

        return trim(collect([$currentDetail, $entry])
            ->filter(fn (?string $detail): bool => filled($detail))
            ->implode("\n\n"));
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
            ->title('Tarea asignada')
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
