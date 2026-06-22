<x-filament-panels::page>
    {{-- Filtro por responsable --}}
    <div class="mb-6">
        <form wire:submit.prevent="render">
            <select
                wire:model.live="userFilter"
                class="filament-input rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white"
            >
                <option value="">Todos los responsables</option>
                @foreach($this->getUsers() as $user)
                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                @endforeach
            </select>
        </form>
    </div>

    {{-- Resumen del embudo --}}
    @php
        $grouped = $this->getGroupedOpportunities();
        $totalAbiertas = 0;
        $totalMontoAbierto = 0;
        foreach(['prospeccion','calificacion','propuesta','negociacion'] as $key) {
            $totalAbiertas += $grouped[$key]['count'];
            $totalMontoAbierto += $grouped[$key]['total_monto'];
        }
        $totalGanadas = $grouped['ganada']['count'];
        $montoGanado = $grouped['ganada']['total_monto'];
        $totalPerdidas = $grouped['perdida']['count'];
    @endphp

    <div class="grid grid-cols-2 md:grid-cols-5 gap-4 mb-6">
        <div class="bg-white dark:bg-gray-800 rounded-lg p-4 shadow-sm border border-gray-200 dark:border-gray-700">
            <div class="text-sm text-gray-500 dark:text-gray-400">Abiertas</div>
            <div class="text-2xl font-bold text-gray-900 dark:text-white">{{ $totalAbiertas }}</div>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-lg p-4 shadow-sm border border-gray-200 dark:border-gray-700">
            <div class="text-sm text-gray-500 dark:text-gray-400">Monto abierto</div>
            <div class="text-2xl font-bold text-yellow-600 dark:text-yellow-400">${{ number_format($totalMontoAbierto, 0, ',', '.') }}</div>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-lg p-4 shadow-sm border border-gray-200 dark:border-gray-700">
            <div class="text-sm text-gray-500 dark:text-gray-400">Ganadas</div>
            <div class="text-2xl font-bold text-success-600 dark:text-success-400">{{ $totalGanadas }}</div>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-lg p-4 shadow-sm border border-gray-200 dark:border-gray-700">
            <div class="text-sm text-gray-500 dark:text-gray-400">Monto ganado</div>
            <div class="text-2xl font-bold text-success-600 dark:text-success-400">${{ number_format($montoGanado, 0, ',', '.') }}</div>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-lg p-4 shadow-sm border border-gray-200 dark:border-gray-700">
            <div class="text-sm text-gray-500 dark:text-gray-400">Perdidas</div>
            <div class="text-2xl font-bold text-danger-600 dark:text-danger-400">{{ $totalPerdidas }}</div>
        </div>
    </div>

    {{-- Tablero Kanban --}}
    <div class="flex gap-4 overflow-x-auto pb-4" style="min-height: 500px;">
        @php
            $etapaColors = [
                'prospeccion' => ['bg' => 'bg-gray-50 dark:bg-gray-900', 'border' => 'border-gray-300', 'header' => 'bg-gray-100 dark:bg-gray-800', 'text' => 'text-gray-700 dark:text-gray-300'],
                'calificacion' => ['bg' => 'bg-blue-50 dark:bg-blue-950', 'border' => 'border-blue-300', 'header' => 'bg-blue-100 dark:bg-blue-900', 'text' => 'text-blue-700 dark:text-blue-300'],
                'propuesta' => ['bg' => 'bg-yellow-50 dark:bg-yellow-950', 'border' => 'border-yellow-300', 'header' => 'bg-yellow-100 dark:bg-yellow-900', 'text' => 'text-yellow-700 dark:text-yellow-300'],
                'negociacion' => ['bg' => 'bg-purple-50 dark:bg-purple-950', 'border' => 'border-purple-300', 'header' => 'bg-purple-100 dark:bg-purple-900', 'text' => 'text-purple-700 dark:text-purple-300'],
                'ganada' => ['bg' => 'bg-green-50 dark:bg-green-950', 'border' => 'border-green-300', 'header' => 'bg-green-100 dark:bg-green-900', 'text' => 'text-green-700 dark:text-green-300'],
                'perdida' => ['bg' => 'bg-red-50 dark:bg-red-950', 'border' => 'border-red-300', 'header' => 'bg-red-100 dark:bg-red-900', 'text' => 'text-red-700 dark:text-red-300'],
            ];
        @endphp

        @foreach($grouped as $key => $column)
            @php $colors = $etapaColors[$key]; @endphp
            <div class="flex-shrink-0 w-72 {{ $colors['bg'] }} rounded-lg border {{ $colors['border'] }}">
                {{-- Header de columna --}}
                <div class="{{ $colors['header'] }} px-4 py-3 rounded-t-lg border-b {{ $colors['border'] }}">
                    <div class="flex items-center justify-between">
                        <h3 class="font-semibold text-sm {{ $colors['text'] }}">{{ $column['label'] }}</h3>
                        <span class="inline-flex items-center justify-center px-2 py-0.5 text-xs font-medium rounded-full bg-white/60 dark:bg-black/20 {{ $colors['text'] }}">
                            {{ $column['count'] }}
                        </span>
                    </div>
                    @if($column['total_monto'] > 0)
                        <div class="text-xs mt-1 {{ $colors['text'] }} opacity-75">
                            ${{ number_format($column['total_monto'], 0, ',', '.') }}
                        </div>
                    @endif
                </div>

                {{-- Tarjetas de oportunidades --}}
                <div class="p-2 space-y-2" style="min-height: 100px;">
                    @forelse($column['opportunities'] as $opp)
                        <div class="bg-white dark:bg-gray-800 rounded-lg p-3 shadow-sm border border-gray-200 dark:border-gray-700 hover:shadow-md transition-shadow">
                            {{-- Nombre --}}
                            <a href="{{ \App\Filament\Resources\OpportunityResource::getUrl('view', ['record' => $opp]) }}"
                               class="font-medium text-sm text-gray-900 dark:text-white hover:text-primary-600 dark:hover:text-primary-400 line-clamp-2">
                                {{ $opp->nombre }}
                            </a>

                            {{-- Cliente --}}
                            <div class="text-xs text-gray-500 dark:text-gray-400 mt-1 truncate">
                                {{ $opp->cliente->nombre_cuenta ?? 'Sin cliente' }}
                            </div>

                            {{-- Monto --}}
                            @if($opp->monto_estimado)
                                <div class="text-sm font-semibold text-success-600 dark:text-success-400 mt-2">
                                    ${{ number_format($opp->monto_estimado, 0, ',', '.') }}
                                </div>
                            @endif

                            {{-- Probabilidad --}}
                            <div class="mt-2">
                                <div class="flex items-center justify-between text-xs text-gray-500 dark:text-gray-400 mb-1">
                                    <span>{{ $opp->probabilidad }}%</span>
                                    @if($opp->fecha_esperada_cierre)
                                        <span>Cierre: {{ $opp->fecha_esperada_cierre->format('d/m') }}</span>
                                    @endif
                                </div>
                                <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-1.5">
                                    <div class="bg-primary-500 h-1.5 rounded-full transition-all"
                                         style="width: {{ $opp->probabilidad }}%"></div>
                                </div>
                            </div>

                            {{-- Responsable --}}
                            <div class="text-xs text-gray-400 dark:text-gray-500 mt-2">
                                {{ $opp->user->name ?? '—' }}
                            </div>

                            {{-- Acciones --}}
                            @if(!in_array($key, ['ganada', 'perdida']))
                                <div class="flex items-center gap-1 mt-2 pt-2 border-t border-gray-100 dark:border-gray-700">
                                    @if($key !== 'prospeccion')
                                        <button wire:click="retrocederEtapa({{ $opp->id }})"
                                                class="text-xs px-2 py-1 rounded bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600 transition"
                                                title="Retroceder etapa">
                                            ←
                                        </button>
                                    @endif
                                    @if($key !== 'negociacion')
                                        <button wire:click="avanzarEtapa({{ $opp->id }})"
                                                class="text-xs px-2 py-1 rounded bg-primary-100 dark:bg-primary-900 text-primary-700 dark:text-primary-300 hover:bg-primary-200 dark:hover:bg-primary-800 transition"
                                                title="Avanzar etapa">
                                            →
                                        </button>
                                    @endif
                                    @if($key !== 'ganada')
                                        <button wire:click="marcarGanada({{ $opp->id }})"
                                                wire:confirm="¿Marcar esta oportunidad como ganada?"
                                                class="text-xs px-2 py-1 rounded bg-success-100 dark:bg-success-900 text-success-700 dark:text-success-300 hover:bg-success-200 dark:hover:bg-success-800 transition"
                                                title="Marcar ganada">
                                            ✓
                                        </button>
                                    @endif
                                    @if($key !== 'perdida')
                                        <button wire:click="marcarPerdida({{ $opp->id }})"
                                                wire:confirm="¿Marcar esta oportunidad como perdida?"
                                                class="text-xs px-2 py-1 rounded bg-danger-100 dark:bg-danger-900 text-danger-700 dark:text-danger-300 hover:bg-danger-200 dark:hover:bg-danger-800 transition"
                                                title="Marcar perdida">
                                            ✗
                                        </button>
                                    @endif
                                </div>
                            @endif
                        </div>
                    @empty
                        <div class="text-center text-xs text-gray-400 dark:text-gray-500 py-8">
                            Sin oportunidades
                        </div>
                    @endforelse
                </div>
            </div>
        @endforeach
    </div>
</x-filament-panels::page>
