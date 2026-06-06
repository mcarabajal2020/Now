@if(isset($payments) && count($payments))
    <div class="grid grid-cols-1 gap-2">
        @foreach($payments as $p)
            <div class="p-3 border rounded-lg bg-white shadow-lg">
                <div class="flex justify-between items-center">
                    <div class="font-semibold">
                        @if(! empty($p['url']))
                            <a href="{{ $p['url'] }}" class="text-primary">{{ $p['titulo'] }}</a>
                        @else
                            {{ $p['titulo'] }}
                        @endif
                    </div>
                    <div class="text-sm text-gray-500">{{ is_string($p['fecha']) ? $p['fecha'] : (method_exists($p['fecha'],'format') ? $p['fecha']->format('d/m/Y H:i') : (string)$p['fecha']) }}</div>
                </div>
                <div class="text-sm text-gray-700 mt-1">Estado: {{ $p['estado'] ?? '-' }}</div>
                <div class="text-sm text-gray-700">{{ $p['detalle'] ?? '-' }}</div>
                @if(isset($p['importe_pagado']) && ! is_null($p['importe_pagado']))
                    <div class="text-sm text-gray-700 font-medium">Importe pagado: {{ number_format((float) $p['importe_pagado'], 2, ',', '.') }}</div>
                @endif
            </div>
            @unless($loop->last)
                <hr class="border-t-2 border-gray-300 my-3" />
            @endunless
        @endforeach
    </div>
@else
    <div class="text-gray-500">No hay pedidos de fondos.</div>
@endif
