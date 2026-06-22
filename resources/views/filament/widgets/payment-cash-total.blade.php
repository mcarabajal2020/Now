<x-filament-widgets::widget>
    <x-filament::section heading="Total pendiente de pago">
        <div class="flex flex-col gap-4">
            <div class="flex items-end gap-3">
                <span
                    class="text-3xl font-bold tracking-tight text-danger-600 dark:text-danger-400"
                    wire:loading.class="opacity-50"
                >
                    ${{ number_format($totalPendiente, 0, ',', '.') }}
                </span>
                <span class="text-sm text-gray-500 dark:text-gray-400 mb-1">
                    {{ $countPendientes }} pedido{{ $countPendientes !== 1 ? 's' : '' }} sin finalizar
                </span>
            </div>

            <div>
                <label for="fecha_pago" class="fi-fo-field-wrp-label inline-block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                    Fecha de pago
                </label>
                <input
                    type="date"
                    id="fecha_pago"
                    wire:model.live="fecha"
                    class="fi-input w-full rounded-lg border border-gray-300 bg-white py-2 px-3 text-sm shadow-sm dark:border-gray-600 dark:bg-gray-700 dark:text-white"
                />
            </div>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
