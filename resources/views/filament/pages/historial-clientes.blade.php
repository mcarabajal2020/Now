<div>
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

    @elseif($activeTab === 'oportunidades')
        <div class="space-y-4 mt-4">
            <h2 class="text-lg font-semibold">Oportunidades</h2>
            <div class="grid grid-cols-1 gap-3">
                @forelse($this->getOpportunities() as $item)
                    <div class="p-3 border rounded-lg">
                        <div class="flex items-center justify-between gap-3">
                            <div class="font-semibold">
                                @if(! empty($item['url']))
                                    <a href="{{ $item['url'] }}" class="text-primary hover:underline">{{ $item['nombre'] }}</a>
                                @else
                                    {{ $item['nombre'] }}
                                @endif
                            </div>
                            <div class="text-sm text-gray-500">{{ $item['fecha'] ?
                                (is_string($item['fecha']) ? $item['fecha'] :
                                (method_exists($item['fecha'], 'format') ? $item['fecha']->format('d/m/Y') : (string)$item['fecha'])) : '' }}</div>
                        </div>
                        <div class="text-sm text-gray-700 mt-1">
                            <span class="inline-block px-2 py-0.5 rounded text-xs font-medium
                                @if($item['etapa'] === 'ganada') bg-success-100 text-success-700
                                @elseif($item['etapa'] === 'perdida') bg-danger-100 text-danger-700
                                @elseif($item['etapa'] === 'negociacion') bg-warning-100 text-warning-700
                                @else bg-gray-100 text-gray-700 @endif">
                                {{ $item['etapa_label'] }}
                            </span>
                            @if($item['monto'])
                                <span class="ml-2 font-medium">${{ number_format((float) $item['monto'], 0, ',', '.') }}</span>
                            @endif
                        </div>
                        @if(! empty($item['descripcion']))
                            <div class="text-sm text-gray-500 mt-1">{{ Str::limit($item['descripcion'], 80) }}</div>
                        @endif
                    </div>
                @empty
                    <div class="text-gray-500">No hay oportunidades para este cliente.</div>
                @endforelse
            </div>
        </div>

    @elseif($activeTab === 'actividades')
        <div class="space-y-4 mt-4">
            <h2 class="text-lg font-semibold">Actividades</h2>
            <div class="grid grid-cols-1 gap-3">
                @forelse($this->getActividades() as $item)
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
                            <span class="inline-block px-2 py-0.5 rounded text-xs font-medium
                                @if($item['tipo'] === 'llamada') bg-info-100 text-info-700
                                @elseif($item['tipo'] === 'reunion') bg-warning-100 text-warning-700
                                @elseif($item['tipo'] === 'email') bg-success-100 text-success-700
                                @else bg-gray-100 text-gray-700 @endif">
                                {{ ucfirst($item['tipo']) }}
                            </span>
                            @if($item['oportunidad_nombre'])
                                <span class="ml-2 text-gray-500">→ {{ $item['oportunidad_nombre'] }}</span>
                            @endif
                        </div>
                        @if(! empty($item['descripcion']))
                            <div class="text-sm text-gray-500 mt-1">{{ Str::limit($item['descripcion'], 80) }}</div>
                        @endif
                        @if(! empty($item['resultado']))
                            <div class="text-sm text-gray-600 mt-1"><span class="font-medium">Resultado:</span> {{ $item['resultado'] }}</div>
                        @endif
                    </div>
                @empty
                    <div class="text-gray-500">No hay actividades para este cliente.</div>
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
