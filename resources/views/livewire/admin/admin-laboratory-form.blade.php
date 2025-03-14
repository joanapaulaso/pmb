<div>
    <form action="{{ $this->getFormAction() }}" method="POST" enctype="multipart/form-data" class="p-6">
        @csrf
        @if($isEdit)
            @method('PUT')
        @endif

        <div class="mb-6">
            <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Nome do Laboratório <span class="text-red-500">*</span></label>
            <input type="text" name="name" id="name" value="{{ $name }}" class="shadow-sm focus:ring-green-500 focus:border-green-500 block w-full sm:text-sm border-gray-300 rounded-md" required>
            @error('name')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <div>
                <label for="state_id" class="block text-sm font-medium text-gray-700 mb-1">Estado <span class="text-red-500">*</span></label>
                @livewire('search-select', [
                    'model' => 'states',
                    'field' => 'state_id',
                    'placeholder' => 'Digite para buscar estado...',
                    'initialValue' => $state_id,
                    'key' => 'state-select-' . ($isEdit ? $laboratory->id : 'new')
                ])
                <input type="hidden" name="state_id" id="hidden_state_id" value="{{ $state_id }}">
                @error('state_id')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="institution_id" class="block text-sm font-medium text-gray-700 mb-1">Instituição <span class="text-red-500">*</span></label>
                @livewire('search-select', [
                    'model' => 'institutions',
                    'field' => 'institution_id',
                    'dependsOn' => ['state_id' => $currentStateId ?? null],
                    'placeholder' => 'Digite para buscar instituição...',
                    'initialValue' => $institution_id,
                    'key' => 'institution-select-' . ($isEdit ? $laboratory->id : 'new') . '-' . ($currentStateId ?? 'null')
                ])
                <input type="hidden" name="institution_id" id="hidden_institution_id" value="{{ $institution_id }}">
                @error('institution_id')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div class="mb-6">
            <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Descrição</label>
            <textarea name="description" id="description" rows="3" class="shadow-sm focus:ring-green-500 focus:border-green-500 block w-full sm:text-sm border-gray-300 rounded-md">{{ $description }}</textarea>
            @error('description')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-6">
            <label for="website" class="block text-sm font-medium text-gray-700 mb-1">Website (opcional)</label>
            <input type="url" name="website" id="website" value="{{ $website }}" placeholder="https://www.example.com" class="shadow-sm focus:ring-green-500 focus:border-green-500 block w-full sm:text-sm border-gray-300 rounded-md">
            @error('website')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-6">
            <label for="address" class="block text-sm font-medium text-gray-700 mb-1">Endereço (opcional)</label>
            <input type="text" name="address" id="address" value="{{ $address }}" class="shadow-sm focus:ring-green-500 focus:border-green-500 block w-full sm:text-sm border-gray-300 rounded-md">
            @error('address')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <div>
                <label for="lat" class="block text-sm font-medium text-gray-700 mb-1">Latitude (opcional)</label>
                <input type="text" name="lat" id="lat" value="{{ $lat }}" placeholder="-23.5505" class="shadow-sm focus:ring-green-500 focus:border-green-500 block w-full sm:text-sm border-gray-300 rounded-md">
                @error('lat')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="lng" class="block text-sm font-medium text-gray-700 mb-1">Longitude (opcional)</label>
                <input type="text" name="lng" id="lng" value="{{ $lng }}" placeholder="-46.6333" class="shadow-sm focus:ring-green-500 focus:border-green-500 block w-full sm:text-sm border-gray-300 rounded-md">
                @error('lng')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div class="mb-6">
            <label for="logo" class="block text-sm font-medium text-gray-700 mb-1">Logo (opcional)</label>
            @if($existingLogo)
            <div class="mb-2">
                <img src="{{ asset('storage/' . $existingLogo) }}" alt="Logo atual" class="h-16 w-16 object-cover rounded-md">
                <p class="text-xs text-gray-500 mt-1">Logo atual. Envie uma nova imagem para substituir.</p>
            </div>
            @endif
            <input type="file" name="logo" id="logo" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-green-50 file:text-green-700 hover:file:bg-green-100">
            <p class="mt-1 text-xs text-gray-500">Formatos aceitos: JPG, PNG, GIF. Tamanho máximo: 2MB.</p>
            @error('logo')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div class="flex items-center justify-end">
            <a href="{{ route('admin.laboratories.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 mr-3">
                Cancelar
            </a>

            <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                {{ $isEdit ? 'Atualizar' : 'Salvar' }} Laboratório
            </button>
        </div>
    </form>

    <script>
        document.addEventListener('livewire:initialized', function() {
            // Acompanhar eventos de seleção de opções
            Livewire.on('optionSelected', function(data) {
                if (data.field === 'state_id') {
                    document.getElementById('hidden_state_id').value = data.value || '';
                } else if (data.field === 'institution_id') {
                    document.getElementById('hidden_institution_id').value = data.value || '';
                }
            });

            // Debug: adicionar função para monitorar eventos específicos do Livewire
            console.log("Monitoramento de eventos Livewire inicializado");
        });
    </script>
</div>
