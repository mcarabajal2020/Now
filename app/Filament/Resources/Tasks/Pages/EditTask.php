<?php

namespace App\Filament\Resources\Tasks\Pages;

use App\Filament\Resources\Tasks\TaskResource;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditTask extends EditRecord
{
    protected static string $resource = TaskResource::class;

    private ?int $previousAssignedUserId = null;

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $this->previousAssignedUserId = $this->record->asignado_a_id;

        $newProgressDetail = trim((string) ($data['nuevo_detalle'] ?? ''));

        unset($data['nuevo_detalle']);

        if ($newProgressDetail !== '' && $this->isAssignedToCurrentUser()) {
            $data['detalle'] = $this->appendProgressDetail($this->record->detalle, $newProgressDetail);

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

        if ($this->record->asignado_a_id !== $this->previousAssignedUserId) {
            $this->sendAssignedTaskNotification($this->record->asignado_a_id);
        }
    }

    public function finalize(): void
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

        Notification::make()
            ->success()
            ->title('Tarea finalizada')
            ->send();

        $this->redirect($this->getResource()::getUrl('edit', ['record' => $this->record]), navigate: false);
    }

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
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
            ->requiresConfirmation()
            ->modalHeading('Finalizar tarea')
            ->modalDescription('La tarea pasará a Finalizado y se registrará la fecha de finalización.')
            ->modalSubmitActionLabel('Finalizar')
            ->action(fn (): null => $this->finalize())
            ->visible(fn (): bool => $this->isAssignedToCurrentUser() && $this->record->estado !== 'Finalizado');
    }

    private function isAssignedToCurrentUser(): bool
    {
        return $this->record->asignado_a_id === auth()->id();
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
