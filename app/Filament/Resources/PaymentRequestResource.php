<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PaymentRequestResource\Pages;
use App\Filament\Resources\PaymentRequestResource\RelationManagers\LogsRelationManager;
use App\Filament\Traits\AuthorizedResource;
use App\Models\Cliente;
use App\Models\ClienteCbu;
use App\Models\PaymentRequest;
use App\Models\PaymentRequestLog;
use App\Models\User;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Html;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Storage;

class PaymentRequestResource extends Resource
{
    use AuthorizedResource;

    protected static ?string $model = PaymentRequest::class;

    protected static ?string $navigationLabel = 'Pedidos de fondos';

    protected static ?string $modelLabel = 'Pedido de fondos';

    protected static ?string $pluralModelLabel = 'Pedidos de fondos';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::CurrencyDollar;

    protected static ?int $navigationSort = 4;

    protected static function getPermissionKey(): string
    {
        return 'paymentrequests';
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();

        /** @var User|null $user */
        $user = auth()->user();

        if (! $user || $user->role?->nombre === 'admin' || $user->puede_autorizar || $user->puede_realizar_pago || $user->puede_realizar_transferencia) {
            return $query;
        }

        return $query->where('solicitante_id', $user->id);
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('cliente_tag')
                ->label('Tag de cliente')
                ->numeric()
                ->placeholder('Ej: 123')
                ->helperText('Si se informa, se cargará el cliente asociado a ese tag.')
                ->reactive()
                ->afterStateUpdated(function ($state, callable $set) {
                    $tag = blank($state) ? null : (string) $state;

                    if (blank($tag)) {
                        return;
                    }

                    // tags son numéricos => se guardan como strings dentro del JSON
                    $cliente = Cliente::query()->whereJsonContains('tags', $tag)->first();

                    if (! $cliente) {
                        $set('cliente_id', null);
                        $set('numero_cuenta', null);
                        $set('nombre_cuenta', null);
                        $set('cliente_cbu_id', null);

                        return;
                    }

                    $set('cliente_id', $cliente->id);
                    $set('numero_cuenta', $cliente->numero_cuenta);
                    $set('nombre_cuenta', $cliente->nombre_cuenta);

                    // cliente_cbu_id se recalculará cuando cargue el cliente_id
                    $set('cliente_cbu_id', null);
                })
                ->dehydrated(false),

            Select::make('cliente_id')
                ->label('Cliente')
                ->options(fn () => Cliente::all()->mapWithKeys(fn ($c) => [$c->id => ($c->numero_cuenta.' - '.$c->nombre_cuenta)])->toArray())
                ->searchable()
                ->preload()
                ->reactive()
                ->disabled(fn (?PaymentRequest $record): bool => ! static::canUpdateRequestDetails($record))
                ->dehydrated(fn (?PaymentRequest $record): bool => static::canUpdateRequestDetails($record))
                ->afterStateUpdated(function ($state, callable $set) {
                    if ($state) {
                        $cliente = Cliente::find($state);
                        $set('numero_cuenta', $cliente?->numero_cuenta);
                        $set('nombre_cuenta', $cliente?->nombre_cuenta);
                        $set('cliente_cbu_id', null);
                    }
                }),

            TextInput::make('numero_cuenta')
                ->label('Número de cuenta')
                ->disabled()
                ->dehydrated(fn (?PaymentRequest $record): bool => static::canUpdateRequestDetails($record)),

            TextInput::make('nombre_cuenta')
                ->label('Nombre de cuenta')
                ->disabled()
                ->dehydrated(fn (?PaymentRequest $record): bool => static::canUpdateRequestDetails($record)),

            Select::make('cliente_cbu_id')
                ->label('CBU')
                ->options(fn ($get) => $get('cliente_id')
                    ? ClienteCbu::where('cliente_id', $get('cliente_id'))
                        ->get()
                        ->mapWithKeys(fn ($c) => [$c->id => $c->cbu.' ('.$c->tipo_cbu.')'])
                        ->toArray()
                    : [])
                ->native(false)
                ->searchable()
                ->disabled(fn (?PaymentRequest $record): bool => ! static::canUpdateRequestDetails($record))
                ->dehydrated(fn (?PaymentRequest $record): bool => static::canUpdateRequestDetails($record))
                ->required(),

            TextInput::make('monto')
                ->label('Monto solicitado')
                ->numeric()
                ->disabled(fn (?PaymentRequest $record): bool => ! static::canUpdateRequestDetails($record))
                ->dehydrated(fn (?PaymentRequest $record): bool => static::canUpdateRequestDetails($record))
                ->required(),

            TextInput::make('total_pagado')
                ->label('Total pagado')
                ->numeric()
                ->disabled(fn (?PaymentRequest $record): bool => ! static::canUpdateRequestDetails($record))
                ->dehydrated(fn (?PaymentRequest $record): bool => static::canUpdateRequestDetails($record))
                ->required(),

            DatePicker::make('fecha_pago')->label('Fecha de pago')->native(false),

            Textarea::make('observaciones')
                ->label('Observaciones')
                ->columnSpanFull(),

            Textarea::make('observaciones_pago')
                ->label('Observaciones (pago)')
                ->columnSpanFull(),

            Html::make(fn () => view('filament.components.payment-request-images-preview', [
                'images' => (function () {
                    $id = request()->route('record');
                    if (! $id) {
                        return [];
                    }
                    $r = PaymentRequest::find($id);

                    return $r?->imagenes ?? [];
                })(),
            ])->render()),

            FileUpload::make('imagenes')
                ->label('Imágenes')
                ->multiple()
                ->disk('public')
                ->visibility('public')
                ->directory('payment-requests')
                ->image()
                ->enableOpen()
                ->maxSize(10240)
                ->columnSpanFull()
                ->formatStateUsing(function ($state) {
                    if (is_null($state)) {
                        return null;
                    }

                    $items = is_array($state) ? $state : (array) $state;
                    $normalized = [];

                    foreach ($items as $item) {
                        if (! is_string($item)) {
                            $normalized[] = $item;

                            continue;
                        }

                        // Si es una URL completa que contiene '/storage/', extraer la ruta relativa
                        if (str_starts_with($item, 'http')) {
                            $path = parse_url($item, PHP_URL_PATH) ?: $item;
                            if (str_contains($path, '/storage/')) {
                                $rel = ltrim(substr($path, strpos($path, '/storage/') + strlen('/storage/')), '/');
                                // Construir URL con host actual para evitar 'http://localhost' incorrecto
                                $host = request()?->getSchemeAndHttpHost() ?: rtrim(config('filesystems.disks.public.url'), '/');
                                $normalized[] = $host.'/storage/'.$rel;

                                continue;
                            }
                        }

                        // Si la cadena contiene '/storage/' aunque no sea URL
                        if (str_contains($item, '/storage/')) {
                            $rel = ltrim(substr($item, strpos($item, '/storage/') + strlen('/storage/')), '/');
                            $host = request()?->getSchemeAndHttpHost() ?: rtrim(config('filesystems.disks.public.url'), '/');
                            $normalized[] = $host.'/storage/'.$rel;

                            continue;
                        }

                        // Construir URL relativa desde la ruta almacenada
                        $host = request()?->getSchemeAndHttpHost() ?: rtrim(config('filesystems.disks.public.url'), '/');
                        $normalized[] = $host.'/storage/'.ltrim($item, '/');
                    }

                    return $normalized;
                })
                ->disabled(fn (?PaymentRequest $record): bool => ! static::canUpdateRequestDetails($record))
                ->dehydrated(fn (?PaymentRequest $record): bool => static::canUpdateRequestDetails($record))
                ->preserveFilenames(),
            // Script para permitir pegar imágenes desde el portapapeles
            Html::make(fn () => view('filament.components.clipboard-paste-upload')->render()),
        ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Pedido')
                ->schema([
                    TextEntry::make('cliente.numero_cuenta')
                        ->label('Número de cuenta')
                        ->getStateUsing(fn (PaymentRequest $record): ?string => $record->cliente?->numero_cuenta ?? $record->numero_cuenta),

                    TextEntry::make('cliente.nombre_cuenta')
                        ->label('Nombre de cuenta')
                        ->getStateUsing(fn (PaymentRequest $record): ?string => $record->cliente?->nombre_cuenta ?? $record->nombre_cuenta),

                    TextEntry::make('clienteCbu.cbu')
                        ->label('CBU'),

                    TextEntry::make('estado')
                        ->label('Estado')
                        ->formatStateUsing(fn (?string $state): ?string => match ($state) {
                            'pendiente_autorizacion' => 'Pendiente autorización',
                            'pendiente_pago' => 'Pendiente pago',
                            'pendiente_transferencia' => 'Pendiente transferencia',
                            'terminado' => 'Terminado',
                            'cancelado' => 'Cancelado',
                            default => $state,
                        }),

                    TextEntry::make('monto')
                        ->label('Monto solicitado')
                        ->formatStateUsing(fn ($state): ?string => is_null($state) ? null : (number_format((float) $state, 2, ',', '.').' ARS')),

                    TextEntry::make('total_pagado')
                        ->label('Total pagado')
                        ->formatStateUsing(fn ($state): ?string => is_null($state) ? null : (number_format((float) $state, 2, ',', '.').' ARS')),

                    TextEntry::make('fecha_pago')
                        ->label('Fecha de pago')
                        ->date(),

                    TextEntry::make('observaciones')
                        ->label('Observaciones'),

                    TextEntry::make('observaciones_pago')
                        ->label('Observaciones (pago)')
                        ->columnSpanFull(),

                    Html::make(fn () => view('filament.components.payment-request-images-preview', [
                        'images' => (function () {
                            $id = request()->route('record');
                            if (! $id) {
                                return [];
                            }
                            $r = PaymentRequest::find($id);

                            return $r?->imagenes ?? [];
                        })(),
                    ])->render())
                        ->columnSpanFull(),
                ])
                ->columns(2),

            Section::make('Auditoría')
                ->schema([
                    TextEntry::make('solicitante.name')->label('Solicitante'),
                    TextEntry::make('autorizadoPor.name')->label('Autorizó'),
                    TextEntry::make('pagadoPor.name')->label('Pagó'),
                    TextEntry::make('transferidoPor.name')->label('Transfirió'),
                    TextEntry::make('created_at')->label('Creado')->dateTime(),
                    TextEntry::make('updated_at')->label('Actualizado')->dateTime(),
                ])
                ->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('cliente.numero_cuenta')
                    ->label('Número de cuenta')
                    ->searchable(['numero_cuenta'])
                    ->getStateUsing(fn ($record) => $record->cliente?->numero_cuenta ?? $record->numero_cuenta),

                TextColumn::make('cliente.nombre_cuenta')
                    ->label('Nombre de cuenta')
                    ->searchable(['nombre_cuenta'])
                    ->getStateUsing(fn ($record) => $record->cliente?->nombre_cuenta ?? $record->nombre_cuenta),

                TextColumn::make('monto')
                    ->label('Monto')
                    ->formatStateUsing(fn ($state) => is_null($state) ? null : (number_format((float) $state, 2, ',', '.').' ARS')),

                TextColumn::make('solicitante.name')->label('Solicitante'),

                TextColumn::make('estado')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pendiente_autorizacion' => 'danger',      // Rojo
                        'pendiente_pago' => 'warning',    // amarillo
                        'pendiente_transferencia' => 'info',  // celeste
                        'terminado' => 'success',  // verde
                    })
                    ->label('Estado')
                    ->formatStateUsing(fn ($state) => match ($state) {
                        'pendiente_autorizacion' => 'Pendiente autorización',
                        'pendiente_pago' => 'Pendiente pago',
                        'pendiente_transferencia' => 'Pendiente transferencia',
                        'terminado' => 'Terminado',
                        default => $state,
                    })
                    ->sortable(),

                TextColumn::make('autorizadoPor.name')->label('Autorizó')->sortable(),
                TextColumn::make('pagadoPor.name')->label('Pagó')->sortable(),
                TextColumn::make('transferidoPor.name')->label('Transfirió')->sortable(),

            ])
            ->filters([
                // Filtro solo afecta a la lista (no al formulario de creación/edición)

                Filter::make('fecha_pago')
                    ->form([
                        DatePicker::make('from_fecha_pago')->label('Desde'),
                        DatePicker::make('to_fecha_pago')->label('Hasta'),
                    ])
                    ->query(function (Builder $query, array $data) {
                        return $query
                            ->when($data['from_fecha_pago'] ?? null, fn (Builder $q, $date) => $q->whereDate('fecha_pago', '>=', $date))
                            ->when($data['to_fecha_pago'] ?? null, fn (Builder $q, $date) => $q->whereDate('fecha_pago', '<=', $date));
                    }),

                SelectFilter::make('tag')
                    ->label('Tag de cliente')
                    ->options(fn () => Cliente::query()
                        ->whereNotNull('tags')
                        ->get()
                        ->flatMap(fn ($c) => (array) $c->tags)
                        ->unique()
                        ->sort()
                        ->values()
                        ->mapWithKeys(fn ($t) => [$t => $t])
                        ->toArray()
                    )
                    ->query(function (Builder $query, array $data) {
                        $value = $data['value'] ?? null;

                        if (blank($value)) {
                            return $query;
                        }

                        return $query->whereHas('cliente', function (Builder $q) use ($value) {
                            $q->whereJsonContains('tags', $value);
                        });
                    }),

                SelectFilter::make('estado')
                    ->label('Estado')
                    ->options([
                        'pendiente_autorizacion' => 'Pendiente autorización',
                        'pendiente_pago' => 'Pendiente pago',
                        'pendiente_transferencia' => 'Pendiente transferencia',
                        'terminado' => 'Terminado',
                    ]),
            ])
            ->recordActions([
                ViewAction::make()->label('Ver'),

                Action::make('authorize')
                    ->label('Autorizar')
                    ->visible(fn ($record) => auth()->user()?->puede_autorizar && $record->estado === 'pendiente_autorizacion')
                    ->action(function (PaymentRequest $record) {
                        $record->update([
                            'estado' => 'pendiente_pago',
                            'autorizado_por_id' => auth()->id(),
                            'autorizado_at' => now(),
                        ]);
                        PaymentRequestLog::create([
                            'payment_request_id' => $record->id,
                            'event' => 'autorizado',
                            'user_id' => auth()->id(),
                            'message' => null,
                            'created_at' => now(),
                        ]);
                    }),

                Action::make('mark_paid')
                    ->label('Pago realizado')
                    ->visible(fn ($record) => auth()->user()?->puede_realizar_pago && $record->estado === 'pendiente_pago')
                    ->action(function (PaymentRequest $record) {
                        $record->update([
                            'estado' => 'pendiente_transferencia',
                            'pagado_por_id' => auth()->id(),
                            'pagado_at' => now(),
                        ]);
                        PaymentRequestLog::create([
                            'payment_request_id' => $record->id,
                            'event' => 'pagado',
                            'user_id' => auth()->id(),
                            'message' => null,
                            'created_at' => now(),
                        ]);
                    }),

                Action::make('transfer')
                    ->label('Transferencia realizada')
                    ->visible(fn ($record) => auth()->user()?->puede_realizar_transferencia && $record->estado === 'pendiente_transferencia')
                    ->action(function (PaymentRequest $record) {
                        $record->update([
                            'estado' => 'terminado',
                            'transferido_por_id' => auth()->id(),
                            'transferido_at' => now(),
                        ]);
                        PaymentRequestLog::create([
                            'payment_request_id' => $record->id,
                            'event' => 'transferido',
                            'user_id' => auth()->id(),
                            'message' => null,
                            'created_at' => now(),
                        ]);
                    }),

                Action::make('cancel')
                    ->label('Cancelar')
                    ->visible(fn (PaymentRequest $record): bool => auth()->user()?->role?->nombre === 'admin' && ! in_array($record->estado, ['terminado'], true))
                    ->form([
                        TextInput::make('cancelacion_observaciones')
                            ->label('Observaciones de cancelación')
                            ->maxLength(2000),
                    ])
                    ->action(function (PaymentRequest $record, array $data) {
                        $record->update([
                            'estado' => 'cancelado',
                            'cancelado_por_id' => auth()->id(),
                            'cancelado_at' => now(),
                            'cancelacion_observaciones' => blank($data['cancelacion_observaciones'] ?? null) ? null : (string) $data['cancelacion_observaciones'],
                        ]);

                        PaymentRequestLog::create([
                            'payment_request_id' => $record->id,
                            'event' => 'cancelado',
                            'user_id' => auth()->id(),
                            'message' => blank($data['cancelacion_observaciones'] ?? null) ? null : (string) $data['cancelacion_observaciones'],
                            'created_at' => now(),
                        ]);
                    }),

                EditAction::make()
                    ->label('Editar')
                    ->visible(fn (PaymentRequest $record): bool => ! in_array($record->estado, ['terminado', 'cancelado'], true)),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            LogsRelationManager::class,
        ];
    }

    public static function canUpdateRequestDetails(?PaymentRequest $record): bool
    {
        if (! $record) {
            return true;
        }

        if ($record->estado === 'terminado') {
            return false;
        }

        /** @var User|null $user */
        $user = auth()->user();

        if (! $user) {
            return false;
        }

        return $user->role?->nombre === 'admin' || $record->solicitante_id === $user->id;
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPaymentRequests::route('/'),
            'create' => Pages\CreatePaymentRequest::route('/create'),
            'view' => Pages\ViewPaymentRequest::route('/{record}'),
            'edit' => Pages\EditPaymentRequest::route('/{record}/edit'),
        ];
    }
}
