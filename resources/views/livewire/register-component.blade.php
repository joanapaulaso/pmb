<div class="min-h-screen flex items-center justify-center bg-gray-50 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full p-8 bg-white rounded-lg shadow-md">
        <div class="p-5 shrink-0 flex items-center justify-center">
            <a href="{{ route('portal') }}">
                <x-application-mark class="block h-9 w-auto" />
            </a>
        </div>
        <h2 class="text-center text-2xl font-bold text-[#267e90] mb-6">Criar uma Conta</h2>

        <form wire:submit.prevent="submit" class="space-y-6">
            <!-- Personal Information Section -->
            <div>
                <label class="block text-sm font-medium text-gray-700">Nome Completo</label>
                <input type="text" wire:model="full_name" class="w-full px-4 py-2 border rounded-md">
                @error('full_name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Email</label>
                <input type="email" wire:model="email" class="w-full px-4 py-2 border rounded-md">
                @error('email') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>

            <!-- Campo de Gênero (Novo) -->
            <div>
                <label class="block text-sm font-medium text-gray-700">Gênero</label>
                <select wire:model="gender" class="w-full px-4 py-2 border rounded-md">
                    <option value="">Selecione um gênero</option>
                    <option value="feminino">Feminino</option>
                    <option value="masculino">Masculino</option>
                    <option value="nao-binario">Não-binário</option>
                    <option value="outro">Outro</option>
                    <option value="prefiro-nao-dizer">Prefiro não dizer</option>
                </select>
                @error('gender') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Senha</label>
                <input type="password" wire:model="password" class="w-full px-4 py-2 border rounded-md">
                @error('password') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Confirmar Senha</label>
                <input type="password" wire:model="password_confirmation" class="w-full px-4 py-2 border rounded-md">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Data de Nascimento</label>
                <input type="date" wire:model="birth_date" class="w-full px-4 py-2 border rounded-md">
                @error('birth_date') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>

            <!-- Location Section -->
            <div class="pt-4 border-t border-gray-200">
                <h3 class="font-medium text-gray-700 mb-2">Localização</h3>

                <div class="mb-4">
                    <label class="inline-flex items-center">
                        <input type="checkbox" wire:model.live="isInternational" class="form-checkbox">
                        <span class="ml-2 text-sm">Não sou do Brasil</span>
                    </label>
                </div>

                <!-- Brazilian Fields -->
                <div class="{{ $isInternational ? 'hidden' : '' }}">
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">Estado</label>
                        <livewire:search-select
                            model="states"
                            field="state_id"
                            placeholder="Digite para buscar estado..."
                            :initialValue="$state_id"
                            :key="'state-' . $isInternational"
                        />
                        @error('state_id') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">Município</label>
                        <livewire:search-select
                            model="municipalities"
                            field="municipality_id"
                            :dependsOn="['state_id' => $state_id]"
                            placeholder="Digite para buscar município..."
                            :initialValue="$municipality_id"
                            :key="'municipality-' . $state_id . '-' . $isInternational"
                        />
                        @error('municipality_id') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>
                </div>

                <!-- International Fields -->
                <div class="{{ !$isInternational ? 'hidden' : '' }}">
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">País</label>
                        <livewire:search-select
                            model="countries"
                            field="country_code"
                            placeholder="Digite para buscar país..."
                            :initialValue="$country_code"
                            :key="'country-' . $isInternational"
                        />
                        @error('country_code') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>

            <!-- Institution Section -->
            <div class="pt-4 border-t border-gray-200">
                <h3 class="font-medium text-gray-700 mb-2">Instituição</h3>

                <!-- Institution Field -->
                <div class="{{ $showNewInstitution ? 'hidden' : 'mb-4' }}">
                    <label class="block text-sm font-medium text-gray-700">Instituição</label>
                    <livewire:search-select
                        model="institutions"
                        field="institution_id"
                        :dependsOn="['state_id' => $state_id]"
                        placeholder="Digite para buscar instituição..."
                        :initialValue="$institution_id"
                        :key="'institution-' . $state_id"
                    />
                    @error('institution_id') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>

                <div class="mb-4">
                    <label class="inline-flex items-center">
                        <input type="checkbox" wire:model.live="showNewInstitution" class="form-checkbox">
                        <span class="ml-2 text-sm">Minha instituição não está na lista</span>
                    </label>
                </div>

                <!-- New Institution Field -->
                <div class="{{ !$showNewInstitution ? 'hidden' : 'mb-4' }}">
                    <label class="block text-sm font-medium text-gray-700">Nova Instituição</label>
                    <input type="text" wire:model="new_institution" class="w-full px-4 py-2 border rounded-md">
                    @error('new_institution') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>
            </div>

            <!-- Laboratory Section -->
            <div class="pt-4 border-t border-gray-200">
                <h3 class="font-medium text-gray-700 mb-2">Laboratório</h3>

                <!-- Laboratory Field (only when using existing institution) -->
                <div class="{{ ($showNewInstitution || $showNewLaboratory) ? 'hidden' : 'mb-4' }}">
                    <label class="block text-sm font-medium text-gray-700">Laboratório</label>
                    <livewire:search-select
                        model="laboratories"
                        field="laboratory_id"
                        :dependsOn="['institution_id' => $institution_id]"
                        placeholder="Digite para buscar laboratório..."
                        :initialValue="$laboratory_id"
                        :key="'laboratory-' . $institution_id"
                    />
                    @error('laboratory_id') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>

                <div class="mb-4">
                    <label class="inline-flex items-center">
                        <input type="checkbox" wire:model.live="showNewLaboratory" class="form-checkbox">
                        <span class="ml-2 text-sm">Meu laboratório não está na lista</span>
                    </label>
                </div>

                <!-- New Laboratory Field (conditionally shown) -->
                <div class="{{ !$showNewLaboratory ? 'hidden' : 'mb-4' }}">
                    <label class="block text-sm font-medium text-gray-700">Novo Laboratório</label>
                    <input type="text" wire:model="new_laboratory" class="w-full px-4 py-2 border rounded-md">
                    @error('new_laboratory') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="inline-flex items-center">
                        <input type="checkbox" wire:model="lab_coordinator" class="form-checkbox">
                        <span class="ml-2">Coordenador de Laboratório?</span>
                    </label>
                </div>
            </div>

            <!-- Categorias Section -->
            <div class="pt-4 border-t border-gray-200">
                <h3 class="font-medium text-gray-700 mb-2">Categorias de Interesse (opcional)</h3>
                <p class="text-sm text-gray-500 mb-3">Selecione até 3 subcategorias de qualquer categoria.</p>

                <!-- Dropdown para Categoria -->
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700">Categoria</label>
                    <select wire:model.live="selectedCategory" class="w-full px-4 py-2 border rounded-md">
                        <option value="">Selecione uma categoria</option>
                        @foreach(array_keys($categories) as $category)
                            <option value="{{ $category }}">{{ $category }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Dropdown para Subcategoria (apenas exibido quando uma categoria está selecionada) -->
                <div class="mb-4 {{ $selectedCategory ? '' : 'hidden' }}">
                    <label class="block text-sm font-medium text-gray-700">Subcategoria</label>
                    <div class="flex">
                        <select id="subcategory-select" class="w-full px-4 py-2 border rounded-md">
                            <option value="">Selecione uma subcategoria</option>
                            @if($selectedCategory)
                                @foreach($subcategories as $subcategory)
                                    <option value="{{ $subcategory }}">{{ $subcategory }}</option>
                                @endforeach
                            @endif
                        </select>
                        <button
                            type="button"
                            class="ml-2 px-4 py-2 bg-blue-500 text-white rounded-md"
                            x-data
                            x-on:click="
                                const select = document.getElementById('subcategory-select');
                                const value = select.options[select.selectedIndex].value;
                                if (value) $wire.addSubcategory(value);
                            "
                        >
                            Adicionar
                        </button>
                    </div>
                    <p class="text-xs text-gray-500 mt-1">Selecionadas: {{ count($selectedSubcategories) }}/3</p>
                </div>

                <!-- Exibição das seleções -->
                @if(count($selectedSubcategories) > 0)
                    <div class="p-4 bg-gray-50 rounded-md mb-4">
                        <h4 class="text-sm font-medium text-gray-700 mb-2">Subcategorias selecionadas:</h4>
                        <ul class="space-y-2">
                            @foreach($selectedSubcategories as $index => $item)
                                <li class="flex justify-between items-center p-2 bg-white rounded border">
                                    <span class="text-sm text-gray-700">{{ $item['category'] }} / {{ $item['subcategory'] }}</span>
                                    <button
                                        type="button"
                                        class="text-red-500 hover:text-red-700"
                                        wire:click="removeSubcategory({{ $index }})"
                                    >
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </button>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif
            </div>

            <div>
                <button type="submit" class="w-full bg-blue-500 text-white py-2 px-4 rounded-md shadow">
                    Registrar
                </button>
            </div>
        </form>
    </div>
</div>
