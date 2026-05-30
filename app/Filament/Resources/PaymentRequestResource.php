<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PaymentRequestResource\Pages;
use App\Filament\Resources\PaymentRequestResource\RelationManagers\LogsRelationManager;
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
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class PaymentRequestResource extends Resource
{
    protected static ?string $model = PaymentRequest::class;

    protected static ?string $navigationLabel = 'Pedidos de fondos';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::CurrencyDollar;

    protected static ?int $navigationSort = 3;

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
                ->options(fn ($get) => $get('cliente_id') ? ClienteCbu::where('cliente_id', $get('cliente_id'))->pluck('cbu', 'id') : [])
                ->disabled(fn (?PaymentRequest $record): bool => ! static::canUpdateRequestDetails($record))
                ->dehydrated(fn (?PaymentRequest $record): bool => static::canUpdateRequestDetails($record))
                ->required(),

            TextInput::make('monto')
                ->label('Monto solicitado')
                ->numeric()
                ->disabled(fn (?PaymentRequest $record): bool => ! static::canUpdateRequestDetails($record))
                ->dehydrated(fn (?PaymentRequest $record): bool => static::canUpdateRequestDetails($record))
                ->required(),

            DatePicker::make('fecha_pago')->label('Fecha de pago')->native(false),

            Textarea::make('observaciones')->label('Observaciones')->columnSpanFull(),
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

                    TextEntry::make('monto')
                        ->label('Monto solicitado')
                        ->formatStateUsing(fn ($state): ?string => is_null($state) ? null : (number_format((float) $state, 2, ',', '.').' ARS')),

                    TextEntry::make('fecha_pago')
                        ->label('Fecha de pago')
                        ->date(),

                    TextEntry::make('estado')
                        ->label('Estado')
                        ->formatStateUsing(fn ($state) => match ($state) {
                            'pendiente_autorizacion' => 'Pendiente autorización',
                            'pendiente_pago' => 'Pendiente pago',
                            'pendiente_transferencia' => 'Pendiente transferencia',
                            'terminado' => 'Terminado',
                            default => $state,
                        }),

                    TextEntry::make('observaciones')
                        ->label('Observaciones')
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
                ViewAction::make(),

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

                EditAction::make()
                    ->visible(fn (PaymentRequest $record): bool => $record->estado !== 'terminado'),
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
