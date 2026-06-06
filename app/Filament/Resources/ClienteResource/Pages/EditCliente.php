<?php

namespace App\Filament\Resources\ClienteResource\Pages;

use App\Filament\Resources\ClienteResource;
use App\Filament\Resources\ClienteResource\Pages;
use Filament\Resources\Pages\EditRecord;
use Filament\Actions\Action;

class EditCliente extends EditRecord
{
    protected static string $resource = ClienteResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('historial')
                ->label('Historial')
                ->icon('heroicon-o-clock')
                ->modalHeading('Historial del cliente')
                ->modalWidth('lg')
                ->schema([
                    \Filament\Schemas\Components\Tabs::make()
                        ->tabs([
                            \Filament\Schemas\Components\Tabs\Tab::make('tareas')
                                ->label('Tareas')
                                ->schema([
                                    \Filament\Schemas\Components\Html::make(fn () => view('filament.pages.historial-tabs-tareas', [
                                        'tasks' => \App\Models\Task::query()->where('cliente_id', $this->record->id)->orderByDesc('ultima_modificacion')->orderByDesc('fecha_creacion')->get()->map(fn ($task) => [
                                            'id' => $task->id,
                                            'titulo' => $task->titulo,
                                            'detalle' => $task->detalle,
                                            'estado' => $task->estado,
                                            'fecha' => $task->ultima_modificacion ?? $task->fecha_creacion,
                                            'url' => \App\Filament\Resources\Tasks\TaskResource::getUrl('edit', ['record' => $task->id]),
                                        ]),
                                    ])->render()),
                                ]),

                            \Filament\Schemas\Components\Tabs\Tab::make('fondos')
                                ->label('Pedidos de fondos')
                                ->schema([
                                    \Filament\Schemas\Components\Html::make(fn () => view('filament.pages.historial-tabs-fondos', [
                                        'payments' => \App\Models\PaymentRequest::query()->where('cliente_id', $this->record->id)->orderByDesc('fecha_pago')->orderByDesc('created_at')->get()->map(fn ($p) => [
                                            'id' => $p->id,
                                            'titulo' => 'Pedido de fondos',
                                            'detalle' => $p->observaciones,
                                            'estado' => $p->estado,
                                            'fecha' => $p->fecha_pago ?? $p->created_at,
                                            'importe_pagado' => $p->total_pagado ?? $p->monto ?? null,
                                            'url' => \App\Filament\Resources\PaymentRequestResource::getUrl('view', ['record' => $p->id]),
                                        ]),
                                    ])->render()),
                                ]),
                        ])
                        ->persistTabInQueryString('tab'),
                ]),
        ];
    }
}
