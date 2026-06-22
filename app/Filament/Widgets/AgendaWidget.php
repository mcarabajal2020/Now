<?php

namespace App\Filament\Widgets;

use App\Models\Actividad;
use Filament\Actions\BulkActionGroup;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;

class AgendaWidget extends TableWidget
{
    protected static ?int $sort = 5;

    protected static ?string $heading = 'Agenda proxima';

    public function table(Table $table): Table
    {
        $query = Actividad::query()
            ->with(['cliente', 'oportunidad', 'user'])
            ->where('fecha', '>=', now()->toDateString())
            ->orderBy('fecha', 'asc')
            ->orderBy('hora_inicio', 'asc');

        return $table
            ->query($query)
            ->columns([
                TextColumn::make('fecha')
                    ->label('Fecha')
                    ->date('d/m')
                    ->sortable(),

                TextColumn::make('hora_inicio')
                    ->label('Hora')
                    ->time('H:i'),

                TextColumn::make('tipo')
                    ->label('Tipo')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'llamada' => 'info',
                        'reunion' => 'warning',
                        'email' => 'success',
                        default => 'gray',
                    }),

                TextColumn::make('titulo')
                    ->label('Titulo')
                    ->limit(30)
                    ->searchable(),

                TextColumn::make('cliente.nombre_cuenta')
                    ->label('Cliente')
                    ->placeholder('N/A'),

                TextColumn::make('oportunidad.nombre')
                    ->label('Oportunidad')
                    ->placeholder('N/A'),
            ])
            ->defaultPaginationPageOption(5)
            ->filters([])
            ->headerActions([])
            ->recordActions([])
            ->toolbarActions([
                BulkActionGroup::make([]),
            ]);
    }
}
