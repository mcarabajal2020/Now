<div class="space-y-4">
    {{-- Botón "Historial" se implementa como página propia en ClienteResource (tabs internas) --}}

    <div class="flex gap-3 items-center">
        <a href="{{ url()->current() }}?tab=tareas" @class([
            'px-3 py-2 rounded-lg border text-sm',
            'bg-white' => ($tab ?? 'tareas') === 'tareas',
            'bg-gray-50 text-gray-700' => ($tab ?? 'tareas') !== 'tareas',
        ])>Tareas</a>

        <a href="{{ url()->current() }}?tab=fondos" @class([
            'px-3 py-2 rounded-lg border text-sm',
            'bg-white' => ($tab ?? 'tareas') === 'fondos',
            'bg-gray-50 text-gray-700' => ($tab ?? 'tareas') !== 'fondos',
        ])>Pedidos de fondos</a>
    </div>

    @php
        $activeTab = request()->query('tab', 'tareas');
    @endphp

    @if($activeTab === 'fondos')
        <div class="space-y-3">
            <h3 class="text-base font-semibold">Pedidos de fondos</h3>

            @forelse($this->getPaymentRequests() as $item)
                <div class="p-3 border rounded-lg">
                    <div class="flex items-center justify-between gap-3">
                        <div class="font-semibold">{{ $item['titulo'] }}</div>
                        <div class="text-sm text-gray-500">
                            {{ $item['fecha'] ? (is_object($item['fecha']) && method_exists($item['fecha'], 'format') ? $item['fecha']->format('d/m/Y H:i') : $item['fecha']) : '' }}
                        </div>
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
    @else
        <div class="space-y-3">
            <h3 class="text-base font-semibold">Tareas</h3>

            @forelse($this->getTasks() as $item)
                <div class="p-3 border rounded-lg">
                    <div class="flex items-center justify-between gap-3">
                        <div class="font-semibold">{{ $item['titulo'] }}</div>
                        <div class="text-sm text-gray-500">
                            {{ $item['fecha'] ? (is_object($item['fecha']) && method_exists($item['fecha'], 'format') ? $item['fecha']->format('d/m/Y H:i') : $item['fecha']) : '' }}
                        </div>
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
    @endif
</div>

