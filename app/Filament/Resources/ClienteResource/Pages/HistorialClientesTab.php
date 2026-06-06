<?php

namespace App\Filament\Resources\ClienteResource\Pages;

use App\Models\Cliente;
use App\Models\PaymentRequest;
use App\Models\Task;
use Filament\Pages\Page;

class HistorialClientesTab extends Page
{
    protected static string $resource = \App\Filament\Resources\ClienteResource::class;


    public ?int $record = null;

    public function getRecord(): ?Cliente
    {
        return $this->record ? Cliente::find($this->record) : null;
    }
}

