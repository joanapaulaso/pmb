<form wire:submit.prevent="store" enctype="multipart/form-data" class="p-6 space-y-6">
    <!-- Nome -->
    <div>
        <label for="name" class="block text-sm font-medium text-gray-700">Nome</label>
        <input 
            type="text" 
            wire:model="name" 
            id="name" 
            class="mt-1 block w-full px-4 py-2 border-1 border-gray-300 rounded text-gray-700 focus:ring-2 focus:ring-blue-500 focus:outline-transparent transition-colors" 
            required
        >
        @error('name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
    </div>

    <!-- Estado e Instituição -->
    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
        <div>
            <label for="state_id" class="block text-sm font-medium text-gray-700">Estado</label>
            <livewire:admin.laboratory-search-select
                model="states"
                field="state_id"
                placeholder="Digite para buscar estado..."
                wire:model.live="state_id"
            />
            @error('state_id') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
        </div>
        <div>
            <label for="institution_id" class="block text-sm font-medium text-gray-700">Instituição</label>
            <livewire:admin.laboratory-search-select
                model="institutions"
                field="institution_id"
                :dependsOn="['state_id' => $state_id]"
                placeholder="Digite para buscar instituição..."
                wire:model.live="institution_id"
            />
            @error('institution_id') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
        </div>
    </div>

    <!-- Descrição -->
    <div>
        <label for="description" class="block text-sm font-medium text-gray-700">Descrição</label>
        <textarea 
            wire:model="description" 
            id="description" 
            class="mt-1 block w-full px-4 py-2 border-1 border-gray-300 rounded text-gray-700 focus:ring-2 focus:ring-blue-500 focus:outline-transparent transition-colors"
        ></textarea>
        @error('description') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
    </div>

    <!-- Website -->
    <div>
        <label for="website" class="block text-sm font-medium text-gray-700">Website</label>
        <input 
            type="url" 
            wire:model="website" 
            id="website" 
            class="mt-1 block w-full px-4 py-2 border-1 border-gray-300 rounded text-gray-700 focus:ring-2 focus:ring-blue-500 focus:outline-transparent transition-colors"
        >
        @error('website') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
    </div>

    <!-- Endereço com Place Picker -->
    <div>
        <label for="address" class="block text-sm font-medium text-gray-700">Endereço</label>
        <gmpx-place-picker
            id="address"
            placeholder="Digite o endereço do laboratório..."
            class="mt-1 block w-full px-4 py-2 border-1 border-gray-300 rounded text-gray-700 focus:ring-2 focus:ring-blue-500 focus:outline-transparent transition-colors"
        ></gmpx-place-picker>
        @error('address') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
    </div>

    <!-- Mapa -->
    <div>
        <label class="block text-sm font-medium text-gray-700">Localização no Mapa</label>
        <livewire:admin.map-component :lat="$lat" :lng="$lng" :address="$address" />
    </div>

    <!-- Campos adicionais -->
    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
        <div>
            <label for="building" class="block text-sm font-medium text-gray-700">Prédio</label>
            <input 
                type="text" 
                wire:model="building" 
                id="building" 
                class="mt-1 block w-full px-4 py-2 border-1 border-gray-300 rounded text-gray-700 focus:ring-2 focus:ring-blue-500 focus:outline-transparent transition-colors"
            >
            @error('building') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
        </div>
        <div>
            <label for="floor" class="block text-sm font-medium text-gray-700">Andar</label>
            <input 
                type="text" 
                wire:model="floor" 
                id="floor" 
                class="mt-1 block w-full px-4 py-2 border-1 border-gray-300 rounded text-gray-700 focus:ring-2 focus:ring-blue-500 focus:outline-transparent transition-colors"
            >
            @error('floor') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
        </div>
    </div>

    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
        <div>
            <label for="room" class="block text-sm font-medium text-gray-700">Sala</label>
            <input 
                type="text" 
                wire:model="room" 
                id="room" 
                class="mt-1 block w-full px-4 py-2 border-1 border-gray-300 rounded text-gray-700 focus:ring-2 focus:ring-blue-500 focus:outline-transparent transition-colors"
            >
            @error('room') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
        </div>
        <div>
            <label for="department" class="block text-sm font-medium text-gray-700">Departamento</label>
            <input 
                type="text" 
                wire:model="department" 
                id="department" 
                class="mt-1 block w-full px-4 py-2 border-1 border-gray-300 rounded text-gray-700 focus:ring-2 focus:ring-blue-500 focus:outline-transparent transition-colors"
            >
            @error('department') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
        </div>
    </div>

    <div>
        <label for="campus" class="block text-sm font-medium text-gray-700">Campus</label>
        <input 
            type="text" 
            wire:model="campus" 
            id="campus" 
            class="mt-1 block w-full px-4 py-2 border-1 border-gray-300 rounded text-gray-700 focus:ring-2 focus:ring-blue-500 focus:outline-transparent transition-colors"
        >
        @error('campus') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
    </div>

    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
        <div>
            <label for="phone" class="block text-sm font-medium text-gray-700">Telefone</label>
            <input 
                type="text" 
                wire:model="phone" 
                id="phone" 
                class="mt-1 block w-full px-4 py-2 border-1 border-gray-300 rounded text-gray-700 focus:ring-2 focus:ring-blue-500 focus:outline-transparent transition-colors"
            >
            @error('phone') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
        </div>
        <div>
            <label for="contact_email" class="block text-sm font-medium text-gray-700">Email de Contato</label>
            <input 
                type="email" 
                wire:model="contact_email" 
                id="contact_email" 
                class="mt-1 block w-full px-4 py-2 border-1 border-gray-300 rounded text-gray-700 focus:ring-2 focus:ring-blue-500 focus:outline-transparent transition-colors"
            >
            @error('contact_email') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
        </div>
    </div>

    <div>
        <label for="working_hours" class="block text-sm font-medium text-gray-700">Horário de Funcionamento</label>
        <input 
            type="text" 
            wire:model="working_hours" 
            id="working_hours" 
            class="mt-1 block w-full px-4 py-2 border-1 border-gray-300 rounded text-gray-700 focus:ring-2 focus:ring-blue-500 focus:outline-transparent transition-colors"
        >
        @error('working_hours') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
    </div>

    <div>
        <label class="inline-flex items-center">
            <input 
                type="checkbox" 
                wire:model="has_accessibility" 
                id="has_accessibility" 
                class="form-checkbox h-5 w-5 text-blue-500 border-1 border-gray-300 rounded"
            >
            <span class="ml-2 text-sm text-gray-700">Possui Acessibilidade</span>
        </label>
        @error('has_accessibility') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
    </div>

    <!-- Logo -->
    <div>
        <label for="logo" class="block text-sm font-medium text-gray-700">Logo</label>
        <input 
            type="file" 
            wire:model="logo" 
            id="logo" 
            class="mt-1 block w-full text-gray-700 file:mr-4 file:py-2 file:px-4 file:rounded file:border-0 file:bg-blue-500 file:text-white hover:file:bg-blue-600 transition-colors"
        >
        @error('logo') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
    </div>

    <!-- Botões -->
    <div class="flex justify-end gap-4">
        <a 
            href="{{ route('admin.laboratories.index') }}" 
            class="px-4 py-2 text-sm text-gray-700 bg-gray-200 rounded shadow hover:bg-gray-300 transition-colors"
        >
            Cancelar
        </a>
        <button 
            type="submit" 
            class="px-4 py-2 text-sm text-white bg-blue-500 rounded shadow hover:bg-blue-600 active:bg-blue-700 transition-colors"
        >
            Criar
        </button>
    </div>
</form>