@if(isset($oportunidades) && count($oportunidades))
    <div class="grid grid-cols-1 gap-2">
        @foreach($oportunidades as $o)
            <div class="p-3 border rounded-lg bg-white shadow-lg">
                <div class="flex justify-between items-center">
                    <div class="font-semibold">
                        @if(! empty($o['url']))
                            <a href="{{ $o['url'] }}" class="text-primary">{{ $o['nombre'] }}</a>
                        @else
                            {{ $o['nombre'] }}
                        @endif
                    </div>
                    <div class="text-sm text-gray-500">{{ $o['fecha'] ? (is_string($o['fecha']) ? $o['fecha'] : (method_exists($o['fecha'],'format') ? $o['fecha']->format('d/m/Y') : (string)$o['fecha'])) : '' }}</div>
                </div>
                <div class="text-sm text-gray-700 mt-1">
                    <span class="inline-block px-2 py-0.5 rounded text-xs font-medium
                        @if($o['etapa'] === 'ganada') bg-success-100 text-success-700
                        @elseif($o['etapa'] === 'perdida') bg-danger-100 text-danger-700
                        @elseif($o['etapa'] === 'negociacion') bg-warning-100 text-warning-700
                        @else bg-gray-100 text-gray-700 @endif">
                        {{ $o['etapa_label'] }}
                    </span>
                    @if($o['monto'])
                        <span class="ml-2 font-medium">${{ number_format((float) $o['monto'], 0, ',', '.') }}</span>
                    @endif
                </div>
                @if(! empty($o['descripcion']))
                    <div class="text-sm text-gray-500 mt-1">{{ Str::limit($o['descripcion'], 80) }}</div>
                @endif
            </div>
            @unless($loop->last)
                <hr class="border-t-2 border-gray-300 my-3" />
            @endunless
        @endforeach
    </div>
@else
    <div class="text-gray-500">No hay oportunidades.</div>
@endif
