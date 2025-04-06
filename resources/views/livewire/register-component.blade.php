<div class="min-h-screen flex items-center justify-center bg-stone-50 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full p-8 bg-white rounded-lg shadow">
        <div class="p-5 shrink-0 flex items-center justify-center">
            <a href="{{ route('portal') }}">
                <x-application-mark class="block h-9 w-auto" />
            </a>
        </div>
        <h2 class="text-center text-2xl font-bold text-[#267e90] mb-6 tracking-tight">Criar uma Conta</h2>

        <form wire:submit.prevent="submit" class="space-y-6">
            <!-- Nome Completo -->
            <div>
                <label class="block text-sm font-medium text-gray-700">Nome Completo</label>
                <input type="text" wire:model.live="full_name" class="w-full px-4 py-2 border-1 border-gray-300 rounded text-gray-700 focus:ring-2 focus:ring-blue-500 focus:outline-transparent transition-colors">
                @error('full_name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>

            <!-- Email -->
            <div>
                <label class="block text-sm font-medium text-gray-700">Email</label>
                <input type="email" wire:model.live="email" class="w-full px-4 py-2 border-1 border-gray-300 rounded text-gray-700 focus:ring-2 focus:ring-blue-500 focus:outline-transparent transition-colors">
                @error('email') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>

            <!-- Gênero -->
            <div>
                <label class="block text-sm font-medium text-gray-700">Gênero</label>
                <select wire:model.live="gender" class="w-full px-4 py-2 border-1 border-gray-300 rounded text-gray-700 focus:ring-2 focus:ring-blue-500 focus:outline-transparent transition-colors">
                    <option value="">Selecione um gênero</option>
                    <option value="feminino">Feminino</option>
                    <option value="masculino">Masculino</option>
                    <option value="nao-binario">Não-binário</option>
                    <option value="outro">Outro</option>
                    <option value="prefiro-nao-dizer">Prefiro não dizer</option>
                </select>
                @error('gender') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>

            <!-- Senha -->
            <div>
                <label class="block text-sm font-medium text-gray-700">Senha</label>
                <input type="password" wire:model.live="password" class="w-full px-4 py-2 border-1 border-gray-300 rounded text-gray-700 focus:ring-2 focus:ring-blue-500 focus:outline-transparent transition-colors">
                @error('password') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>

            <!-- Confirmar Senha -->
            <div>
                <label class="block text-sm font-medium text-gray-700">Confirmar Senha</label>
                <input type="password" wire:model.live="password_confirmation" class="w-full px-4 py-2 border-1 border-gray-300 rounded text-gray-700 focus:ring-2 focus:ring-blue-500 focus:outline-transparent transition-colors">
                @error('password_confirmation') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>

            <!-- Data de Nascimento -->
            <div>
                <label class="block text-sm font-medium text-gray-700">Data de Nascimento</label>
                <input type="date" wire:model.live="birth_date" class="w-full px-4 py-2 border-1 border-gray-300 rounded text-gray-700 focus:ring-2 focus:ring-blue-500 focus:outline-transparent transition-colors">
                @error('birth_date') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>

            <!-- Localização -->
            <div class="pt-4 border-t border-gray-200">
                <h3 class="font-medium text-gray-700 mb-2">Localização</h3>

                <div class="mb-4">
                    <label class="inline-flex items-center">
                        <input type="checkbox" wire:model.live="isInternational" class="form-checkbox text-blue-500">
                        <span class="ml-2 text-sm text-gray-600">Não sou do Brasil</span>
                    </label>
                </div>

                <!-- Campos Brasileiros -->
                <div class="{{ $isInternational ? 'hidden' : '' }}">
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">
                            Estado
                            <span class="inline-block ml-1 text-gray-500 cursor-pointer" title="Comece a digitar e selecione uma opção da lista.">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="currentColor" class="size-4">
                                    <path fill-rule="evenodd" d="M15 8A7 7 0 1 1 1 8a7 7 0 0 1 14 0ZM9 5a1 1 0 1 1-2 0 1 1 0 0 1 2 0ZM6.75 8a.75.75 0 0 0 0 1.5h.75v1.75a.75.75 0 0 0 1.5 0v-2.5A.75.75 0 0 0 8.25 8h-1.5Z" clip-rule="evenodd" />
                                </svg>
                            </span>
                        </label>
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
                        <label class="block text-sm font-medium text-gray-700">
                            Município
                            <span class="inline-block ml-1 text-gray-500 cursor-pointer" title="Comece a digitar e selecione uma opção da lista.">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="currentColor" class="size-4">
                                    <path fill-rule="evenodd" d="M15 8A7 7 0 1 1 1 8a7 7 0 0 1 14 0ZM9 5a1 1 0 1 1-2 0 1 1 0 0 1 2 0ZM6.75 8a.75.75 0 0 0 0 1.5h.75v1.75a.75.75 0 0 0 1.5 0v-2.5A.75.75 0 0 0 8.25 8h-1.5Z" clip-rule="evenodd" />
                                </svg>
                            </span>
                        </label>
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

                <!-- Campos Internacionais -->
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

            <!-- Instituição -->
            <div class="pt-4 border-t border-gray-200">
                <h3 class="font-medium text-gray-700 mb-2">Instituição</h3>

                <div class="{{ $showNewInstitution ? 'hidden' : 'mb-4' }}">
                    <label class="block text-sm font-medium text-gray-700">
                        Instituição
                        <span class="inline-block ml-1 text-gray-500 cursor-pointer" title="Comece a digitar e selecione uma opção da lista. Se não encontrar, marque 'Minha instituição não está na lista'.">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="currentColor" class="size-4">
                                <path fill-rule="evenodd" d="M15 8A7 7 0 1 1 1 8a7 7 0 0 1 14 0ZM9 5a1 1 0 1 1-2 0 1 1 0 0 1 2 0ZM6.75 8a.75.75 0 0 0 0 1.5h.75v1.75a.75.75 0 0 0 1.5 0v-2.5A.75.75 0 0 0 8.25 8h-1.5Z" clip-rule="evenodd" />
                            </svg>
                        </span>
                    </label>
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
                        <input type="checkbox" wire:model.live="showNewInstitution" class="form-checkbox text-blue-500">
                        <span class="ml-2 text-sm text-gray-600">Minha instituição não está na lista</span>
                    </label>
                </div>

                <div class="{{ !$showNewInstitution ? 'hidden' : 'mb-4' }}">
                    <label class="block text-sm font-medium text-gray-700">Nova Instituição</label>
                    <input type="text" wire:model.live="new_institution" class="w-full px-4 py-2 border-1 border-gray-300 rounded text-gray-700 focus:ring-2 focus:ring-blue-500 focus:outline-transparent transition-colors">
                    @error('new_institution') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>
            </div>

            <!-- Laboratório -->
            <div class="pt-4 border-t border-gray-200">
                <h3 class="font-medium text-gray-700 mb-2">Laboratório</h3>

                <div class="{{ ($showNewInstitution || $showNewLaboratory) ? 'hidden' : 'mb-4' }}">
                    <label class="block text-sm font-medium text-gray-700">
                        Laboratório
                        <span class="inline-block ml-1 text-gray-500 cursor-pointer" title="Comece a digitar e selecione uma opção da lista. Se não encontrar, marque 'Meu laboratório não está na lista'.">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="currentColor" class="size-4">
                                <path fill-rule="evenodd" d="M15 8A7 7 0 1 1 1 8a7 7 0 0 1 14 0ZM9 5a1 1 0 1 1-2 0 1 1 0 0 1 2 0ZM6.75 8a.75.75 0 0 0 0 1.5h.75v1.75a.75.75 0 0 0 1.5 0v-2.5A.75.75 0 0 0 8.25 8h-1.5Z" clip-rule="evenodd" />
                            </svg>
                        </span>
                    </label>
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

                <div class="{{ !$showNewInstitution && !$showNewLaboratory ? 'hidden' : 'mb-4' }}">
                    <label class="block text-sm font-medium text-gray-700">Novo Laboratório</label>
                    <input type="text" wire:model.live="new_laboratory" class="w-full px-4 py-2 border-1 border-gray-300 rounded text-gray-700 focus:ring-2 focus:ring-blue-500 focus:outline-transparent transition-colors">
                    @error('new_laboratory') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>

                <div class="{{ !$showNewInstitution ? 'mb-4' : 'hidden' }}">
                    <label class="inline-flex items-center">
                        <input type="checkbox" wire:model.live="showNewLaboratory" class="form-checkbox text-blue-500">
                        <span class="ml-2 text-sm text-gray-600">Meu laboratório não está na lista</span>
                    </label>
                </div>

                <div>
                    <label class="inline-flex items-center">
                        <input type="checkbox" wire:model.live="lab_coordinator" class="form-checkbox text-blue-500">
                        <span class="ml-2 text-gray-700">Coordenador de Laboratório?</span>
                    </label>
                </div>
            </div>

            <!-- Categorias -->
            <div class="pt-4 border-t border-gray-200">
                <h3 class="font-medium text-gray-700 mb-2">Categorias de Interesse (opcional)</h3>
                <p class="text-sm text-gray-500 mb-3 leading-relaxed">Selecione até 3 subcategorias de qualquer categoria.</p>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700">Categoria</label>
                    <select wire:model.live="selectedCategory" class="w-full px-4 py-2 border-1 border-gray-300 rounded text-gray-700 focus:ring-2 focus:ring-blue-500 focus:outline-transparent transition-colors">
                        <option value="">Selecione uma categoria</option>
                        @foreach(array_keys($categories) as $category)
                            <option value="{{ $category }}">{{ $category }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-4 {{ $selectedCategory ? '' : 'hidden' }}">
                    <label class="block text-sm font-medium text-gray-700">Subcategoria</label>
                    <div class="flex gap-2">
                        <select id="subcategory-select" class="w-full px-4 py-2 border-1 border-gray-300 rounded text-gray-700 focus:ring-2 focus:ring-blue-500 focus:outline-transparent transition-colors">
                            <option value="">Selecione uma subcategoria</option>
                            @if($selectedCategory)
                                @foreach($subcategories as $subcategory)
                                    <option value="{{ $subcategory }}">{{ $subcategory }}</option>
                                @endforeach
                            @endif
                        </select>
                        <button type="button" class="px-4 py-2 bg-blue-500 text-white rounded shadow hover:bg-blue-600 active:bg-blue-700 transition-colors" x-data x-on:click="const select = document.getElementById('subcategory-select'); const value = select.options[select.selectedIndex].value; if (value) $wire.addSubcategory(value);">
                            Adicionar
                        </button>
                    </div>
                    <p class="text-xs text-gray-500 mt-1">Selecionadas: {{ count($selectedSubcategories) }}/3</p>
                </div>

                @if(count($selectedSubcategories) > 0)
                    <div class="p-4 bg-stone-50 rounded shadow-inner mb-4">
                        <h4 class="text-sm font-medium text-gray-700 mb-2">Subcategorias selecionadas:</h4>
                        <ul class="space-y-2">
                            @foreach($selectedSubcategories as $index => $item)
                                <li class="flex justify-between items-center p-2 bg-white rounded border-1 border-gray-200">
                                    <span class="text-sm text-gray-700">{{ $item['category'] }} / {{ $item['subcategory'] }}</span>
                                    <button type="button" class="text-red-500 hover:text-red-700 transition-colors" wire:click="removeSubcategory({{ $index }})">
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
                <button type="submit" class="w-full bg-blue-500 text-white py-2 px-4 rounded shadow hover:bg-blue-600 active:bg-blue-700 transition-colors">
                    Registrar
                </button>
            </div>
        </form>
    </div>
</div>