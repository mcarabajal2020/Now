<?php

namespace App\Filament\Resources\OpportunityResource\Pages;

use App\Filament\Resources\OpportunityResource;
use App\Models\Opportunity;
use App\Models\User;
use Filament\Actions\CreateAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Illuminate\Database\Eloquent\Collection;

class KanbanOpportunities extends Page
{
    protected static string $resource = OpportunityResource::class;

    protected static ?string $title = 'Embudo de ventas';

    protected string $view = 'filament.resources.opportunity-resource.kanban';

    public ?string $userFilter = null;

    public function mount(): void
    {
        $this->userFilter = request()->query('user_id');
    }

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('Nueva oportunidad')
                ->icon(Heroicon::Plus)
                ->createAnother(false),
        ];
    }

    public function getGroupedOpportunities(): array
    {
        $etapas = [
            'prospeccion' => 'Prospección',
            'calificacion' => 'Calificación',
            'propuesta' => 'Propuesta',
            'negociacion' => 'Negociación',
            'ganada' => 'Ganada',
            'perdida' => 'Perdida',
        ];

        $query = Opportunity::with(['cliente', 'user'])
            ->orderBy('probabilidad', 'desc')
            ->orderBy('fecha_esperada_cierre', 'asc');

        if ($this->userFilter) {
            $query->where('user_id', $this->userFilter);
        }

        $all = $query->get();

        $grouped = [];
        foreach ($etapas as $key => $label) {
            $items = $all->where('etapa', $key);
            $grouped[$key] = [
                'label' => $label,
                'key' => $key,
                'opportunities' => $items,
                'total_monto' => $items->sum('monto_estimado'),
                'count' => $items->count(),
            ];
        }

        return $grouped;
    }

    public function getUsers(): Collection
    {
        return User::query()->whereHas('role')->orderBy('name')->get();
    }

    public function avanzarEtapa(int $opportunityId): void
    {
        $opp = Opportunity::find($opportunityId);
        if ($opp && ! in_array($opp->etapa, ['ganada', 'perdida'])) {
            $opp->avanzarEtapa();
            Notification::make()
                ->title('Oportunidad avanzada')
                ->body("'{$opp->nombre}' → {$opp->getEtapaLabel()}")
                ->success()
                ->send();
        }
    }

    public function retrocederEtapa(int $opportunityId): void
    {
        $opp = Opportunity::find($opportunityId);
        if ($opp && ! in_array($opp->etapa, ['ganada', 'perdida'])) {
            $opp->retrocederEtapa();
            Notification::make()
                ->title('Oportunidad retrocedida')
                ->body("'{$opp->nombre}' → {$opp->getEtapaLabel()}")
                ->warning()
                ->send();
        }
    }

    public function marcarGanada(int $opportunityId): void
    {
        $opp = Opportunity::find($opportunityId);
        if ($opp && $opp->etapa !== 'ganada') {
            $opp->marcarComoGanada();
            Notification::make()
                ->title('¡Oportunidad ganada!')
                ->body("'{$opp->nombre}' se marcó como ganada")
                ->success()
                ->send();
        }
    }

    public function marcarPerdida(int $opportunityId): void
    {
        $opp = Opportunity::find($opportunityId);
        if ($opp && $opp->etapa !== 'perdida') {
            $opp->marcarComoPerdida();
            Notification::make()
                ->title('Oportunidad perdida')
                ->body("'{$opp->nombre}' se marcó como perdida")
                ->danger()
                ->send();
        }
    }
}
