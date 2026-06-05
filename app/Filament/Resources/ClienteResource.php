<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ClienteResource\Pages;
use App\Filament\Resources\ClienteResource\RelationManagers\CbusRelationManager;
use App\Models\Cliente;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class ClienteResource extends Resource
{
    protected static ?string $model = Cliente::class;

    protected static ?string $navigationLabel = 'Clientes';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::UserCircle;

    protected static ?int $navigationSort = 4;


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
                    })
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
                \Filament\Tables\Filters\SelectFilter::make('tag')
                    ->label('Filtrar por tag')
                    ->options(fn () => \App\Models\Cliente::query()
                        ->whereNotNull('tags')
                        ->get()
                        ->flatMap(fn ($c) => (array) $c->tags)
                        ->unique()
                        ->sort()
                        ->values()
                        ->mapWithKeys(fn ($t) => [$t => $t])
                        ->toArray())
                    ->query(function (\Illuminate\Database\Eloquent\Builder $query, $data) {
                        $value = $data['value'] ?? null;

                        if (blank($value)) {
                            return $query;
                        }

                        return $query->whereJsonContains('tags', $value);
                    }),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                Action::make('import')
                    ->label('Importar clientes')
                    ->icon('heroicon-o-cloud-arrow-up')
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
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListClientes::route('/'),
            'create' => Pages\CreateCliente::route('/create'),
            'edit' => Pages\EditCliente::route('/{record}/edit'),
        ];
    }
}
