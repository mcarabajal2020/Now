<div id="historial-modal" class="space-y-4">
    <div class="flex gap-2" role="tablist">
        <button id="hist-tab-tareas" type="button" data-tab="tareas" aria-controls="hist-content-tareas" aria-selected="true" class="px-3 py-1 rounded underline">Tareas</button>
        <button id="hist-tab-fondos" type="button" data-tab="fondos" aria-controls="hist-content-fondos" aria-selected="false" class="px-3 py-1 rounded">Pedidos de fondos</button>
    </div>

    <div id="hist-content-tareas" class="space-y-3">
        @if(isset($tasks) && count($tasks))
            <div class="grid grid-cols-1 gap-2">
                @foreach($tasks as $t)
                    <div class="p-3 border rounded">
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
                @endforeach
            </div>
        @else
            <div class="text-gray-500">No hay tareas.</div>
        @endif
    </div>

    <div id="hist-content-fondos" class="space-y-3 hidden">
        @if(isset($payments) && count($payments))
            <div class="grid grid-cols-1 gap-2">
                @foreach($payments as $p)
                    <div class="p-3 border rounded">
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
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-gray-500">No hay pedidos de fondos.</div>
        @endif
    </div>
</div>

<script>
    (function () {
        try {
            const modal = document.getElementById('historial-modal');
            if (! modal) return;

            const tabs = {
                tareas: document.getElementById('hist-content-tareas'),
                fondos: document.getElementById('hist-content-fondos'),
            };

            const tabButtons = Array.from(modal.querySelectorAll('[data-tab]'));

            function activate(tabKey) {
                Object.keys(tabs).forEach(k => {
                    const el = tabs[k];
                    if (! el) return;
                    el.classList.toggle('hidden', k !== tabKey);
                });

                tabButtons.forEach(btn => {
                    const is = btn.getAttribute('data-tab') === tabKey;
                    btn.classList.toggle('underline', is);
                    btn.setAttribute('aria-selected', is ? 'true' : 'false');
                });
            }

            tabButtons.forEach(btn => btn.addEventListener('click', () => activate(btn.getAttribute('data-tab'))));

            // Inicializar desde query param 'tab' si existe
            const params = new URLSearchParams(window.location.search);
            const initial = params.get('tab');
            if (initial && tabs[initial]) {
                activate(initial);
            } else {
                activate('tareas');
            }
        } catch (e) {
            // no romper la UI
            console.error(e);
        }
    })();
</script>
