<?php

namespace App\Filament\Resources\Noticias\Pages;

use App\Filament\Resources\Noticias\NoticiaResource;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Collection;

class CreateNoticia extends CreateRecord
{
    protected static string $resource = NoticiaResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $user = auth()->user();

        if (! $user) {
            abort(403);
        }

        $data['creado_por_id'] = $user->id;

        return $data;
    }

    protected function afterCreate(): void
    {
        User::query()
            ->chunkById(100, function (Collection $users): void {
                foreach ($users as $user) {
                    $notification = Notification::make()
                        ->title('Nueva noticia')
                        ->body($this->record->titulo)
                        ->icon('heroicon-o-newspaper')
                        ->actions([
                            Action::make('view')
                                ->label('Ver noticia')
                                ->url(NoticiaResource::getUrl('view', ['record' => $this->record]))
                                ->button()
                                ->markAsRead(),
                        ]);

                    $user->notifyNow($notification->toDatabase());
                }
            });
    }
}
