<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ClienteResource\Pages;
use App\Filament\Resources\ClienteResource\RelationManagers\ActividadesRelationManager;
use App\Filament\Resources\ClienteResource\RelationManagers\CbusRelationManager;
use App\Filament\Resources\Tasks\TaskResource;
use App\Filament\Traits\AuthorizedResource;
use App\Models\Cliente;
use App\Models\PaymentRequest;
use App\Models\Task;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Html as SchemaHtml;
use Filament\Schemas\Components\Tabs as SchemaTabs;
use Filament\Schemas\Components\Tabs\Tab as SchemaTab;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class ClienteResource extends Resource
{
    use AuthorizedResource;

    protected static ?string $model = Cliente::class;

    protected static ?string $navigationLabel = 'Clientes';

    protected static ?string $modelLabel = 'Cliente';

    protected static ?string $pluralModelLabel = 'Clientes';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::UserCircle;

    protected static ?int $navigationSort = 4;

    protected static function getPermissionKey(): string
    {
        return 'clientes';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('numero_cuenta')
                    ->label('Número de cuenta')
                    ->required()
                    ->maxLength(255),

                TextInput::make('nombre_cuenta')
                    ->label('Nombre de cuenta')
                    ->required()
                    ->maxLength(255),

                // TextInput::make('tags_text')
                //   ->label('Tags')
                //   ->helperText('Separá los tags por coma. Ej: EXTERNO1, EXTERNO2')
                //   ->maxLength(2000),

                // Tags son solo referencia externa; no se enganchan a ninguna otra entidad.
                TextInput::make('tags')
                    ->label('Tags')
                    ->placeholder('Ej: ABC,XYZ,OTRO')
                    ->helperText('Separar por coma. Se guardan como lista JSON.')
                    ->columnSpanFull()
                    ->dehydrateStateUsing(function ($state) {
                        if (blank($state)) {
                            return null;
                        }

                        if (is_array($state)) {
                            return array_values(array_filter(array_map(fn ($t) => trim((string) $t), $state)));
                        }

                        $parts = explode(',', (string) $state);

                        return array_values(
                            array_filter(
                                array_map(fn ($t) => trim((string) $t), $parts),
                                fn ($t) => $t !== ''
                            )
                        );
                    }),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('numero_cuenta')->label('Número de cuenta')->searchable(),
                TextColumn::make('nombre_cuenta')->label('Nombre de cuenta')->searchable(),
                TextColumn::make('tags')
                    ->label('Tags')
                    ->formatStateUsing(fn ($state) => is_array($state) ? implode(', ', $state) : ($state ? (string) $state : null))
                    ->toggleable(),
            ])
            ->filters([
                SelectFilter::make('tag')
                    ->label('Filtrar por tag')
                    ->options(fn () => Cliente::query()
                        ->whereNotNull('tags')
                        ->get()
                        ->flatMap(fn ($c) => (array) $c->tags)
                        ->unique()
                        ->sort()
                        ->values()
                        ->mapWithKeys(fn ($t) => [$t => $t])
                        ->toArray())
                    ->query(function (Builder $query, $data) {
                        $value = $data['value'] ?? null;

                        if (blank($value)) {
                            return $query;
                        }

                        return $query->whereJsonContains('tags', $value);
                    }),
            ])
            ->recordActions([
                EditAction::make()->label('Editar'),
                Action::make('historial')
                    ->label('Historial')
                    ->icon('heroicon-o-clock')
                    ->modalHeading('Historial del cliente')
                    ->modalWidth('lg')
                    ->schema([
                        SchemaTabs::make()
                            ->tabs([
                                SchemaTab::make('tareas')
                                    ->label('Tareas')
                                    ->schema([
                                        SchemaHtml::make(fn () => view('filament.pages.historial-tabs-tareas', [
                                            'tasks' => Task::query()->where('cliente_id', request()->route('record') ?? null)->orderByDesc('ultima_modificacion')->orderByDesc('fecha_creacion')->get()->map(fn ($task) => [
                                                'id' => $task->id,
                                                'titulo' => $task->titulo,
                                                'detalle' => $task->detalle,
                                                'estado' => $task->estado,
                                                'fecha' => $task->ultima_modificacion ?? $task->fecha_creacion,
                                                'url' => TaskResource::getUrl('edit', ['record' => $task->id]),
                                            ]),
                                        ])->render()),
                                    ]),

                                SchemaTab::make('fondos')
                                    ->label('Pedidos de fondos')
                                    ->schema([
                                        SchemaHtml::make(fn () => view('filament.pages.historial-tabs-fondos', [
                                            'payments' => PaymentRequest::query()->where('cliente_id', request()->route('record') ?? null)->orderByDesc('fecha_pago')->orderByDesc('created_at')->get()->map(fn ($p) => [
                                                'id' => $p->id,
                                                'titulo' => 'Pedido de fondos',
                                                'detalle' => $p->observaciones,
                                                'estado' => $p->estado,
                                                'fecha' => $p->fecha_pago ?? $p->created_at,
                                                'importe_pagado' => $p->total_pagado ?? $p->monto ?? null,
                                                'url' => PaymentRequestResource::getUrl('view', ['record' => $p->id]),
                                            ]),
                                        ])->render()),
                                    ]),
                            ])
                            ->persistTabInQueryString('tab'),
                    ]),
            ])
            ->toolbarActions([
                Action::make('import')
                    ->label('Importar clientes')
                    ->icon('heroicon-o-cloud-arrow-up')
                    ->tooltip('Parámetros de importación: columnas requeridas: numero_cuenta, nombre_cuenta. Opcionales: banco, cbu, observaciones. Formatos: .csv, .xlsx. Codificación: UTF-8. Separador CSV: coma.')
                    ->extraAttributes(['title' => 'Parámetros de importación: columnas requeridas: numero_cuenta, nombre_cuenta. Opcionales: banco, cbu, observaciones. Formatos: .csv, .xlsx. Codificación: UTF-8. Separador CSV: coma.'])
                    ->form([
                        FileUpload::make('file')
                            ->label('Archivo (.csv o .xlsx)')
                            ->acceptedFileTypes([
                                'text/csv',
                                'text/plain',
                                'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                                'application/vnd.ms-excel',
                            ])
                            ->required(),
                    ])
                    ->action(function (array $data) {
                        $uploaded = $data['file'];

                        // Filament FileUpload puede devolver un UploadedFile o una ruta/identifier string
                        $path = null;
                        $extension = null;

                        if (is_string($uploaded)) {
                            // Intentar ruta absoluta primero
                            if (file_exists($uploaded)) {
                                $path = $uploaded;
                                $extension = pathinfo($path, PATHINFO_EXTENSION);
                            } else {
                                // Intentar resolver vía Storage (ruta devuelta por FileUpload)
                                try {
                                    $storagePath = Storage::path($uploaded);
                                } catch (\Throwable $e) {
                                    $storagePath = null;
                                }

                                if ($storagePath && file_exists($storagePath)) {
                                    $path = $storagePath;
                                    $extension = pathinfo($path, PATHINFO_EXTENSION);
                                }
                            }
                        } elseif (is_object($uploaded) && method_exists($uploaded, 'getRealPath')) {
                            $path = $uploaded->getRealPath();
                            if (method_exists($uploaded, 'getClientOriginalExtension')) {
                                $extension = $uploaded->getClientOriginalExtension();
                            } else {
                                $extension = pathinfo($uploaded->getClientOriginalName() ?? '', PATHINFO_EXTENSION) ?: null;
                            }
                        }

                        if (! $path || ! file_exists($path)) {
                            throw new \Exception('No se pudo procesar el archivo.');
                        }

                        $extension = strtolower((string) ($extension ?? pathinfo($path, PATHINFO_EXTENSION)));

                        $rows = [];

                        if ($extension === 'csv' || $extension === 'txt') {
                            $handle = fopen($path, 'r');
                            if ($handle === false) {
                                throw new \Exception('No se pudo abrir el archivo CSV.');
                            }

                            while (($line = fgetcsv($handle, 0, ',')) !== false) {
                                $rows[] = $line;
                            }

                            fclose($handle);
                        } else {
                            // Usar maatwebsite/excel para xlsx y otros formatos
                            $sheets = Excel::toArray(null, $path);
                            if (empty($sheets) || ! is_array($sheets[0])) {
                                throw new \Exception('Archivo de Excel vacío o inválido.');
                            }

                            $rows = $sheets[0];
                        }

                        if (empty($rows) || ! is_array($rows)) {
                            throw new \Exception('El archivo no contiene filas.');
                        }

                        $header = array_shift($rows);
                        if (! is_array($header)) {
                            throw new \Exception('Cabecera inválida.');
                        }

                        // Normalizar nombres de columnas
                        $normalized = array_map(fn ($h) => strtolower(trim((string) $h)), $header);
                        $map = array_flip($normalized);

                        if (! isset($map['numero_cuenta']) || ! isset($map['nombre_cuenta'])) {
                            throw new \Exception('El archivo debe contener las columnas "numero_cuenta" y "nombre_cuenta".');
                        }

                        foreach ($rows as $row) {
                            if (! is_array($row)) {
                                continue;
                            }

                            $numero = isset($row[$map['numero_cuenta']]) ? trim((string) $row[$map['numero_cuenta']]) : null;
                            $nombre = isset($row[$map['nombre_cuenta']]) ? trim((string) $row[$map['nombre_cuenta']]) : null;

                            $banco = isset($map['banco']) ? ($row[$map['banco']] ?? null) : null;
                            $cbu = isset($map['cbu']) ? ($row[$map['cbu']] ?? null) : null;
                            $tipo_cbu = isset($map['tipo_cbu']) ? ($row[$map['tipo_cbu']] ?? null) : null;
                            $observaciones = isset($map['observaciones']) ? ($row[$map['observaciones']] ?? null) : null;

                            if (blank($numero) || blank($nombre)) {
                                continue;
                            }

                            // Si el número de cuenta ya existe, NO crear/actualizar el cliente,
                            // pero sí agregar/actualizar CBUs si se provee.
                            $existingCliente = Cliente::where('numero_cuenta', $numero)->first();

                            if ($existingCliente) {
                                if (! blank($cbu)) {
                                    $cbuStr = (string) $cbu;
                                    $existingCbu = $existingCliente->cbus()->where('cbu', $cbuStr)->first();

                                    if ($existingCbu) {
                                        // Actualizar datos del CBU si cambiaron
                                        $needsUpdate = false;
                                        $updateData = [];

                                        $bancoVal = blank($banco) ? null : (string) $banco;
                                        $obsVal = blank($observaciones) ? null : (string) $observaciones;
                                        $tipoCbuVal = blank($tipo_cbu) ? null : (string) $tipo_cbu;

                                        if ($existingCbu->banco !== $bancoVal) {
                                            $updateData['banco'] = $bancoVal;
                                            $needsUpdate = true;
                                        }

                                        if ($existingCbu->observaciones !== $obsVal) {
                                            $updateData['observaciones'] = $obsVal;
                                            $needsUpdate = true;
                                        }

                                        if (! is_null($tipoCbuVal) && $existingCbu->tipo_cbu !== $tipoCbuVal) {
                                            $updateData['tipo_cbu'] = $tipoCbuVal;
                                            $needsUpdate = true;
                                        }

                                        if ($needsUpdate) {
                                            $existingCbu->update($updateData);
                                        }
                                    } else {
                                        // Crear nuevo CBU para el cliente
                                        $existingCliente->cbus()->create([
                                            'banco' => blank($banco) ? null : (string) $banco,
                                            'cbu' => $cbuStr,
                                            'tipo_cbu' => blank($tipo_cbu) ? null : (string) $tipo_cbu,
                                            'observaciones' => blank($observaciones) ? null : (string) $observaciones,
                                        ]);
                                    }
                                }

                                continue; // no crear cliente ni modificarlo
                            }

                            // Cliente no existe: crearlo y crear CBU si corresponde
                            $cliente = Cliente::create([
                                'numero_cuenta' => $numero,
                                'nombre_cuenta' => $nombre,
                            ]);

                            if (! blank($cbu)) {
                                $cliente->cbus()->create([
                                    'banco' => blank($banco) ? null : (string) $banco,
                                    'cbu' => (string) $cbu,
                                    'observaciones' => blank($observaciones) ? null : (string) $observaciones,
                                ]);
                            }
                        }
                    }),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            CbusRelationManager::class,
            ActividadesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListClientes::route('/'),
            'create' => Pages\CreateCliente::route('/create'),
            'edit' => Pages\EditCliente::route('/{record}/edit'),
            // Historial del cliente (tareas + pedidos de fondos)
            'historial' => Pages\HistorialClientes::route('/{record}/historial'),

        ];
    }
}
