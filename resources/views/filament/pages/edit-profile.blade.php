<x-filament-panels::page>
    <form wire:submit="save">
        @csrf
        {{ $this->form }}

        <div class="mt-6 flex justify-end">
            <button type="submit" class="inline-flex items-center justify-center rounded-lg border border-transparent bg-primary-600 px-4 py-2 text-sm font-semibold text-white hover:bg-primary-700">
                Guardar cambios
            </button>
        </div>
    </form>
</x-filament-panels::page>
