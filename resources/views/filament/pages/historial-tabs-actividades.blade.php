@if(isset($actividades) && count($actividades))
    <div class="grid grid-cols-1 gap-2">
        @foreach($actividades as $a)
            <div class="p-3 border rounded-lg bg-white shadow-lg">
                <div class="flex justify-between items-center">
                    <div class="font-semibold">
                        @if(! empty($a['url']))
                            <a href="{{ $a['url'] }}" class="text-primary">{{ $a['titulo'] }}</a>
                        @else
                            {{ $a['titulo'] }}
                        @endif
                    </div>
                    <div class="text-sm text-gray-500">{{ $a['fecha'] ? (is_string($a['fecha']) ? $a['fecha'] : (method_exists($a['fecha'],'format') ? $a['fecha']->format('d/m/Y H:i') : (string)$a['fecha'])) : '' }}</div>
                </div>
                <div class="text-sm text-gray-700 mt-1">
                    <span class="inline-block px-2 py-0.5 rounded text-xs font-medium
                        @if($a['tipo'] === 'llamada') bg-info-100 text-info-700
                        @elseif($a['tipo'] === 'reunion') bg-warning-100 text-warning-700
                        @elseif($a['tipo'] === 'email') bg-success-100 text-success-700
                        @else bg-gray-100 text-gray-700 @endif">
                        {{ ucfirst($a['tipo']) }}
                    </span>
                    @if($a['oportunidad_nombre'])
                        <span class="ml-2 text-gray-500">→ {{ $a['oportunidad_nombre'] }}</span>
                    @endif
                </div>
                @if(! empty($a['descripcion']))
                    <div class="text-sm text-gray-500 mt-1">{{ Str::limit($a['descripcion'], 80) }}</div>
                @endif
                @if(! empty($a['resultado']))
                    <div class="text-sm text-gray-600 mt-1"><span class="font-medium">Resultado:</span> {{ $a['resultado'] }}</div>
                @endif
            </div>
            @unless($loop->last)
                <hr class="border-t-2 border-gray-300 my-3" />
            @endunless
        @endforeach
    </div>
@else
    <div class="text-gray-500">No hay actividades.</div>
@endif
