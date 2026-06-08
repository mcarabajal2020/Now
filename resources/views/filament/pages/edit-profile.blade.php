<x-filament-panels::page>
    <form wire:submit.prevent="save">
        {{ $this->form }}

        <div class="mt-8">
            <x-filament::button type="submit">
                Guardar cambios
            </x-filament::button>
        </div>
    </form>
</x-filament-panels::page>
