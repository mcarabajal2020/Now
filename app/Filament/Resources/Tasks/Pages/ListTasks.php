<?php

namespace App\Filament\Resources\Tasks\Pages;

use App\Exports\TasksExport;
use App\Filament\Resources\Tasks\TaskResource;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Maatwebsite\Excel\Facades\Excel;

class ListTasks extends ListRecords
{
    protected static string $resource = TaskResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()->label('Crear ticket'),
            Action::make('export_xlsx')
                ->label('Exportar a Excel')
                ->icon('heroicon-o-document-arrow-down')
                ->color('success')
                ->action(function () {
                    $filename = 'tareas-'.now()->format('Y-m-d_H-i-s').'.xlsx';

                    return Excel::download(new TasksExport($this->getTable()->getQuery()), $filename);
                }),
        ];
    }
}
