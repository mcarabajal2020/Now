<div>
    {{-- Filament Tabs component removed to avoid passing array attributes that break Blade --}}
    {{-- Usamos query param 'tab' para seleccionar la pestaña: ?tab=fondos --}}
    @php
        $activeTab = request()->get('tab', 'tareas');
    @endphp

    @if($activeTab === 'fondos')
        <div class="space-y-4 mt-4">
            <h2 class="text-lg font-semibold">Pedidos de fondos</h2>
            <div class="grid grid-cols-1 gap-3">
                @forelse($this->getPaymentRequests() as $item)
                    <div class="p-3 border rounded-lg">
                        <div class="flex items-center justify-between gap-3">
                            <div class="font-semibold">
                                @if(! empty($item['url']))
                                    <a href="{{ $item['url'] }}" class="text-primary hover:underline">{{ $item['titulo'] }}</a>
                                @else
                                    {{ $item['titulo'] }}
                                @endif
                            </div>
                            <div class="text-sm text-gray-500">{{ $item['fecha'] ? 
                                (is_string($item['fecha']) ? $item['fecha'] : 
                                (method_exists($item['fecha'], 'format') ? $item['fecha']->format('d/m/Y H:i') : (string)$item['fecha'])) : '' }}</div>
                        </div>
                        <div class="text-sm text-gray-700 mt-1">
                            <div><span class="text-gray-500">Estado:</span> {{ $item['estado'] ?? '-' }}</div>
                            <div class="mt-1"><span class="text-gray-500">Observaciones:</span> {{ $item['detalle'] ?? '-' }}</div>
                        </div>
                    </div>
                @empty
                    <div class="text-gray-500">No hay pedidos de fondos para este cliente.</div>
                @endforelse
            </div>
        </div>
    @else
        <div class="space-y-4 mt-4">
            <h2 class="text-lg font-semibold">Tareas</h2>
            <div class="grid grid-cols-1 gap-3">
                @forelse($this->getTasks() as $item)
                    <div class="p-3 border rounded-lg">
                        <div class="flex items-center justify-between gap-3">
                            <div class="font-semibold">
                                @if(! empty($item['url']))
                                    <a href="{{ $item['url'] }}" class="text-primary hover:underline">{{ $item['titulo'] }}</a>
                                @else
                                    {{ $item['titulo'] }}
                                @endif
                            </div>
                            <div class="text-sm text-gray-500">{{ $item['fecha'] ? (is_string($item['fecha']) ? $item['fecha'] : (method_exists($item['fecha'], 'format') ? $item['fecha']->format('d/m/Y H:i') : (string)$item['fecha'])) : '' }}</div>
                        </div>
                        <div class="text-sm text-gray-700 mt-1">
                            <div><span class="text-gray-500">Estado:</span> {{ $item['estado'] ?? '-' }}</div>
                            <div class="mt-1"><span class="text-gray-500">Detalle:</span> {{ $item['detalle'] ?? '-' }}</div>
                        </div>
                    </div>
                @empty
                    <div class="text-gray-500">No hay tareas para este cliente.</div>
                @endforelse
            </div>
        </div>
    @endif
</div>

