@if(isset($tasks) && count($tasks))
    <div class="grid grid-cols-1 gap-2">
        @foreach($tasks as $t)
            <div class="p-3 border rounded-lg bg-white shadow-lg">
                <div class="flex justify-between items-center">
                    <div class="font-semibold">
                        @if(! empty($t['url']))
                            <a href="{{ $t['url'] }}" class="text-primary">{{ $t['titulo'] }}</a>
                        @else
                            {{ $t['titulo'] }}
                        @endif
                    </div>
                    <div class="text-sm text-gray-500">{{ is_string($t['fecha']) ? $t['fecha'] : (method_exists($t['fecha'],'format') ? $t['fecha']->format('d/m/Y H:i') : (string)$t['fecha']) }}</div>
                </div>
                <div class="text-sm text-gray-700 mt-1">{{ $t['detalle'] ?? '-' }}</div>
            </div>
            @unless($loop->last)
                <hr class="border-t-2 border-gray-300 my-3" />
            @endunless
        @endforeach
    </div>
@else
    <div class="text-gray-500">No hay tareas.</div>
@endif
