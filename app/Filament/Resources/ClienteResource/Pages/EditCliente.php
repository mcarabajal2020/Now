<?php

namespace App\Filament\Resources\ClienteResource\Pages;

use App\Filament\Resources\ActividadResource;
use App\Filament\Resources\ClienteResource;
use App\Filament\Resources\OpportunityResource;
use App\Filament\Resources\PaymentRequestResource;
use App\Filament\Resources\Tasks\TaskResource;
use App\Models\Actividad;
use App\Models\Opportunity;
use App\Models\PaymentRequest;
use App\Models\Task;
use Filament\Actions\Action;
use Filament\Resources\Pages\EditRecord;
use Filament\Schemas\Components\Html;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;

class EditCliente extends EditRecord
{
    protected static string $resource = ClienteResource::class;

    protected function getSaveFormAction(): Action
    {
        return parent::getSaveFormAction()->label('Grabar');
    }

    protected function getCancelFormAction(): Action
    {
        return parent::getCancelFormAction()->label('Cancelar');
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('historial')
                ->label('Historial')
                ->icon('heroicon-o-clock')
                ->modalHeading('Historial del cliente')
                ->modalWidth('lg')
                ->schema([
                    Tabs::make()
                        ->tabs([
                            Tab::make('tareas')
                                ->label('Tareas')
                                ->schema([
                                    Html::make(fn () => view('filament.pages.historial-tabs-tareas', [
                                        'tasks' => Task::query()->where('cliente_id', $this->record->id)->orderByDesc('ultima_modificacion')->orderByDesc('fecha_creacion')->get()->map(fn ($task) => [
                                            'id' => $task->id,
                                            'titulo' => $task->titulo,
                                            'detalle' => $task->detalle,
                                            'estado' => $task->estado,
                                            'fecha' => $task->ultima_modificacion ?? $task->fecha_creacion,
                                            'url' => TaskResource::getUrl('edit', ['record' => $task->id]),
                                        ]),
                                    ])->render()),
                                ]),

                            Tab::make('fondos')
                                ->label('Pedidos de fondos')
                                ->schema([
                                    Html::make(fn () => view('filament.pages.historial-tabs-fondos', [
                                        'payments' => PaymentRequest::query()->where('cliente_id', $this->record->id)->orderByDesc('fecha_pago')->orderByDesc('created_at')->get()->map(fn ($p) => [
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

                            Tab::make('oportunidades')
                                ->label('Oportunidades')
                                ->schema([
                                    Html::make(fn () => view('filament.pages.historial-tabs-oportunidades', [
                                        'oportunidades' => Opportunity::query()->where('cliente_id', $this->record->id)->orderByDesc('created_at')->get()->map(fn ($o) => [
                                            'id' => $o->id,
                                            'nombre' => $o->nombre,
                                            'descripcion' => $o->descripcion,
                                            'etapa' => $o->etapa,
                                            'etapa_label' => $o->getEtapaLabel(),
                                            'monto' => $o->monto_estimado,
                                            'fecha' => $o->fecha_esperada_cierre ?? $o->created_at,
                                            'url' => OpportunityResource::getUrl('edit', ['record' => $o->id]),
                                        ]),
                                    ])->render()),
                                ]),

                            Tab::make('actividades')
                                ->label('Actividades')
                                ->schema([
                                    Html::make(fn () => view('filament.pages.historial-tabs-actividades', [
                                        'actividades' => Actividad::query()->where('cliente_id', $this->record->id)->orderByDesc('fecha')->orderByDesc('hora_inicio')->get()->map(fn ($a) => [
                                            'id' => $a->id,
                                            'titulo' => $a->titulo,
                                            'descripcion' => $a->descripcion,
                                            'tipo' => $a->tipo,
                                            'resultado' => $a->resultado,
                                            'fecha' => $a->fecha,
                                            'oportunidad_nombre' => $a->oportunidad?->nombre,
                                            'url' => ActividadResource::getUrl('edit', ['record' => $a->id]),
                                        ]),
                                    ])->render()),
                                ]),
                        ])
                        ->persistTabInQueryString('tab'),
                ]),
        ];
    }
}
