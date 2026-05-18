@push('styles')
<style>
    .form-input {
        @apply mt-1 block w-full rounded-md border border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 px-3 py-2;
    }
</style>
@endpush

<div class="space-y-6 p-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Edit Profile</h1>
    </div>

    <form wire:submit="save" class="space-y-6 bg-white p-6 rounded-lg shadow">
        <div>
            <label for="name" class="block text-sm font-medium text-gray-700">Nombre</label>
            <input type="text" wire:model="name" id="name" class="form-input">
            @error('name')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
        </div>

        <div>
            <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
            <input type="email" wire:model="email" id="email" class="form-input">
            @error('email')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
        </div>

        <div>
            <label for="fecha_nacimiento" class="block text-sm font-medium text-gray-700">Fecha de nacimiento</label>
            <input type="date" wire:model="fecha_nacimiento" id="fecha_nacimiento" class="form-input">
        </div>

        <div>
            <label for="password" class="block text-sm font-medium text-gray-700">Contraseña (opcional)</label>
            <input type="password" wire:model="password" id="password" placeholder="Dejar en blanco para no cambiar" class="form-input">
            @error('password')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
        </div>

        <div class="flex gap-3">
            <button type="submit" class="px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 font-semibold">
                Guardar cambios
            </button>
        </div>
    </form>
</div>
