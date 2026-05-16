<x-filament-panels::page>
    <x-filament-panels::form wire:submit="save">
        {{ $this->form }}

        <x-filament-panels::form.actions>
            <x-filament-panels::form.actions.submit>
                Guardar cambios
            </x-filament-panels::form.actions.submit>
        </x-filament-panels::form.actions>
    </x-filament-panels::form>
</x-filament-panels::page>
