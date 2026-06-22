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
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TaskResource extends Resource
{
    use AuthorizedResource;

    protected static ?string $model = Task::class;

    protected static ?string $navigationLabel = 'Tickets';

    protected static ?string $modelLabel = 'Ticket';

    protected static ?string $pluralModelLabel = 'Tickets';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?int $navigationSort = 2;

    protected static function getPermissionKey(): string
    {
        return 'tasks';
    }

    public static function form(Schema $schema): Schema
    {
        return TaskForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return TasksTable::configure($table)
            ->modifyQueryUsing(function (Builder $query) {
                $user = auth()->user();

                // Eager load cliente para evitar N+1 y asegurar que cliente.numero_cuenta esté disponible
                $query = $query->with('cliente');

                // Detectar término de búsqueda global a partir de query, input o payload anidado
                $search = '';

                // Reglas simples: revisar query params e inputs comunes
                $search = (string) (request()->query('tableSearchQuery') ?? request()->query('tableSearch') ?? request()->query('table_search') ?? request()->query('search') ?? request()->input('tableSearch') ?? request()->input('search') ?? '');

                // Si no está en raíz, buscar recursivamente en el payload por una clave 'search'
                if (trim($search) === '') {
                    $payload = request()->all();
                    $finder = function ($data) use (&$finder) {
                        if (! is_array($data)) {
                            return null;
                        }
                        foreach ($data as $k => $v) {
                            if ($k === 'search' && is_string($v) && trim($v) !== '') {
                                return $v;
                            }
                            if (is_array($v)) {
                                $found = $finder($v);
                                if ($found) {
                                    return $found;
                                }
                            }
                        }

                        return null;
                    };

                    $found = $finder($payload);
                    if ($found) {
                        $search = (string) $found;
                    }
                }

                $search = trim($search);

                if (! empty($search)) {
                    Log::debug('TaskResource table search detected', ['search' => $search, 'payload' => request()->all()]);
                    // Añadir condiciones para buscar por cliente (nombre, número de cuenta, tags)
                    $query->where(function (Builder $q) use ($search) {
                        $q->where('titulo', 'like', "%{$search}%")
                            ->orWhere('detalle', 'like', "%{$search}%")
                            ->orWhereHas('cliente', function (Builder $cq) use ($search) {
                                $cq->where('nombre_cuenta', 'like', "%{$search}%")
                                    ->orWhere('numero_cuenta', 'like', "%{$search}%");

                                // Buscar en JSON 'tags' usando JSON_CONTAINS en MySQL o LIKE como fallback
                                $driver = DB::getDriverName();
                                if ($driver === 'mysql') {
                                    $cq->orWhereRaw('JSON_CONTAINS(tags, ?)', [json_encode((string) $search)]);
                                } else {
                                    $cq->orWhere('tags', 'like', "%{$search}%");
                                }
                            });
                    });
                }

                return $query;
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
