<?php

namespace App\Filament\Resources\Tasks;

use App\Filament\Resources\Tasks\Pages\CreateTask;
use App\Filament\Resources\Tasks\Pages\EditTask;
use App\Filament\Resources\Tasks\Pages\ListTasks;
use App\Filament\Resources\Tasks\RelationManagers\HistoriesRelationManager;
use App\Filament\Resources\Tasks\Schemas\TaskForm;
use App\Filament\Resources\Tasks\Tables\TasksTable;
use App\Filament\Traits\AuthorizedResource;
use App\Models\Task;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class TaskResource extends Resource
{
    use AuthorizedResource;

    protected static ?string $model = Task::class;

    protected static ?string $navigationLabel = 'Tickets';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return TaskForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return TasksTable::configure($table)
            ->modifyQueryUsing(function (Builder $query) {
                $user = auth()->user();

                // Admin ve todas las tareas
                if ($user->role?->nombre === 'admin') {
                    return $query;
                }

                // Multiusuario: ver solicitadas por ti o asignadas a ti
                return $query->where(function (Builder $q) use ($user) {
                    $q->where('usuario_solicita_id', $user->id)
                        ->orWhere('asignado_a_id', $user->id);

                    // Si el usuario pertenece a un área, también ver tareas asignadas a esa área
                    if ($user->area_id) {
                        $q->orWhere('area_id', $user->area_id);
                    }
                });
            });
    }

    public static function getRelations(): array
    {
        return [
            HistoriesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListTasks::route('/'),
            'create' => CreateTask::route('/create'),
            'edit' => EditTask::route('/{record}/edit'),
        ];
    }
}
