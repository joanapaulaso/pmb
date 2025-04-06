<x-form-section submit="updateProfileInformation">
    <x-slot name="title">
        {{ __('Editar Informações do Perfil') }}
    </x-slot>

    <x-slot name="description">
        {{ __('Atualize as informações do perfil da sua conta.') }}
    </x-slot>

    <x-slot name="form">
        <div class="space-y-8">
            <!-- Informações Pessoais -->
            <div class="grid grid-cols-1 gap-6">
                <!-- Nome Completo -->
                <div class="space-y-2">
                    <x-label for="name" value="{{ __('Nome Completo') }}" class="block text-sm font-medium text-gray-700" />
                    <x-input 
                        id="name" 
                        type="text" 
                        class="block w-full" 
                        wire:model.live="state.name" 
                        required 
                        autocomplete="name" 
                    />
                    <x-input-error for="state.name" class="mt-1 text-sm text-red-500" />
                </div>

                <!-- Email -->
                <div class="space-y-2">
                    <x-label for="email" value="{{ __('Email') }}" class="block text-sm font-medium text-gray-700" />
                    <x-input 
                        id="email" 
                        type="email" 
                        class="block w-full" 
                        wire:model.live="state.email" 
                        required 
                        autocomplete="username" 
                    />
                    <x-input-error for="state.email" class="mt-1 text-sm text-red-500" />
                    @if (Laravel\Fortify\Features::enabled(Laravel\Fortify\Features::emailVerification()) && !$this->user->hasVerifiedEmail())
                        <p class="text-sm mt-2 text-gray-600">
                            {{ __('Seu email não está verificado.') }}
                            <button 
                                type="button" 
                                class="underline text-sm text-gray-600 hover:text-gray-900 transition-colors" 
                                wire:click.prevent="sendEmailVerification"
                            >
                                {{ __('Clique aqui para reenviar o email de verificação.') }}
                            </button>
                        </p>
                        @if ($this->verificationLinkSent)
                            <p class="mt-2 font-medium text-sm text-green-600">
                                {{ __('Um novo link de verificação foi enviado para o seu email.') }}
                            </p>
                        @endif
                    @endif
                </div>

                <!-- Data de Nascimento -->
                <div class="space-y-2">
                    <x-label for="birth_date" value="{{ __('Data de Nascimento') }}" class="block text-sm font-medium text-gray-700" />
                    <x-input 
                        id="birth_date" 
                        type="date" 
                        class="block w-full" 
                        wire:model.live="profileData.birth_date" 
                    />
                    <x-input-error for="profileData.birth_date" class="mt-1 text-sm text-red-500" />
                </div>

                <!-- Gênero -->
                <div class="space-y-2">
                    <x-label for="gender" value="{{ __('Gênero') }}" class="block text-sm font-medium text-gray-700" />
                    <select 
                        id="gender" 
                        wire:model.live="profileData.gender" 
                        class="block w-full px-4 py-2 border-1 border-gray-300 rounded text-gray-700 focus:ring-2 focus:ring-blue-500 focus:outline-transparent transition-colors"
                    >
                        <option value="">{{ __('Selecione um gênero') }}</option>
                        <option value="feminino">{{ __('Feminino') }}</option>
                        <option value="masculino">{{ __('Masculino') }}</option>
                        <option value="nao-binario">{{ __('Não-binário') }}</option>
                        <option value="outro">{{ __('Outro') }}</option>
                        <option value="prefiro-nao-dizer">{{ __('Prefiro não dizer') }}</option>
                    </select>
                    <x-input-error for="profileData.gender" class="mt-1 text-sm text-red-500" />
                </div>
            </div>

            <!-- Localização -->
            <div class="pt-6 border-t border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">{{ __('Localização') }}</h3>
                <div class="mt-4 space-y-4">
                    <div class="flex items-center">
                        <x-label for="isInternational" class="inline-flex items-center">
                            <input 
                                type="checkbox" 
                                id="isInternational" 
                                wire:model.live="profileData.isInternational" 
                                class="form-checkbox h-4 w-4 text-blue-500 border-1 border-gray-300 rounded" 
                            />
                            <span class="ml-2 text-sm text-gray-700">{{ __('Não sou do Brasil') }}</span>
                        </x-label>
                    </div>

                    <!-- Campos Brasileiros -->
                    <div class="{{ $profileData['isInternational'] ? 'hidden' : 'block' }} space-y-4">
                        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                            <div class="space-y-2" wire:key="state-search-wrapper">
                                <x-label for="state_id" value="{{ __('Estado') }}" class="block text-sm font-medium text-gray-700" />
                                <livewire:profile.profile-search-select
                                    model="states"
                                    field="state_id"
                                    placeholder="Digite para buscar estado..."
                                    wire:model.live.debounce.500ms="profileData.state_id"
                                    :initialValue="$profileData['state_id']"
                                    :key="'profile-state_id'"
                                />
                                <x-input-error for="profileData.state_id" class="mt-1 text-sm text-red-500" />
                            </div>

                            <div class="space-y-2" wire:key="municipality-search-wrapper">
                                <x-label for="municipality_id" value="{{ __('Município') }}" class="block text-sm font-medium text-gray-700" />
                                <livewire:profile.profile-search-select
                                    model="municipalities"
                                    field="municipality_id"
                                    :dependsOn="['state_id' => $profileData['state_id']]"
                                    placeholder="Digite para buscar município..."
                                    wire:model.live.debounce.500ms="profileData.municipality_id"
                                    :initialValue="$profileData['municipality_id']"
                                    :key="'profile-municipality_id'"
                                />
                                <x-input-error for="profileData.municipality_id" class="mt-1 text-sm text-red-500" />
                            </div>
                        </div>
                    </div>

                    <!-- Campos Internacionais -->
                    <div class="{{ !$profileData['isInternational'] ? 'hidden' : 'block' }} space-y-4">
                        <div class="space-y-2" wire:key="country-search-wrapper">
                            <x-label for="country_code" value="{{ __('País') }}" class="block text-sm font-medium text-gray-700" />
                            <livewire:profile.profile-search-select
                                model="countries"
                                field="country_code"
                                placeholder="Digite para buscar país..."
                                wire:model.live.debounce.500ms="profileData.country_code"
                                :initialValue="$profileData['country_code']"
                                :key="'profile-country_code'"
                            />
                            <x-input-error for="profileData.country_code" class="mt-1 text-sm text-red-500" />
                        </div>
                    </div>
                </div>
            </div>

            <!-- Instituição -->
            <div class="pt-6 border-t border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">{{ __('Instituição') }}</h3>
                <div class="mt-4 space-y-4">
                    <div class="{{ $profileData['showNewInstitution'] ? 'hidden' : 'block' }} space-y-2" wire:key="institution-search-wrapper">
                        <x-label for="institution_id" value="{{ __('Instituição') }}" class="block text-sm font-medium text-gray-700" />
                        <livewire:profile.profile-search-select
                            model="institutions"
                            field="institution_id"
                            :dependsOn="['state_id' => $profileData['state_id']]"
                            placeholder="Digite para buscar instituição..."
                            wire:model.live.debounce.500ms="profileData.institution_id"
                            :initialValue="$profileData['institution_id']"
                            :key="'profile-institution_id'"
                        />
                        <x-input-error for="profileData.institution_id" class="mt-1 text-sm text-red-500" />
                    </div>

                    <div class="flex items-center">
                        <x-label for="showNewInstitution" class="inline-flex items-center">
                            <input 
                                type="checkbox" 
                                id="showNewInstitution" 
                                wire:model.live="profileData.showNewInstitution" 
                                class="form-checkbox h-4 w-4 text-blue-500 border-1 border-gray-300 rounded" 
                            />
                            <span class="ml-2 text-sm text-gray-700">{{ __('Minha instituição não está na lista') }}</span>
                        </x-label>
                    </div>

                    <div class="{{ !$profileData['showNewInstitution'] ? 'hidden' : 'block' }} space-y-2">
                        <x-label for="new_institution" value="{{ __('Nova Instituição') }}" class="block text-sm font-medium text-gray-700" />
                        <x-input 
                            id="new_institution" 
                            type="text" 
                            class="block w-full" 
                            wire:model.live="profileData.new_institution" 
                        />
                        <x-input-error for="profileData.new_institution" class="mt-1 text-sm text-red-500" />
                    </div>

                    <div class="space-y-2">
                        <x-label for="institution_address" value="{{ __('Endereço da Instituição') }}" class="block text-sm font-medium text-gray-700" />
                        <x-input 
                            id="institution_address" 
                            type="text" 
                            class="block w-full" 
                            wire:model.live="profileData.institution_address" 
                        />
                        <x-input-error for="profileData.institution_address" class="mt-1 text-sm text-red-500" />
                    </div>
                </div>
            </div>

            <!-- Laboratório -->
            <div class="pt-6 border-t border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">{{ __('Laboratório') }}</h3>
                <div class="mt-4 space-y-4">
                    <div class="{{ ($profileData['showNewInstitution'] || $profileData['showNewLaboratory']) ? 'hidden' : 'block' }} space-y-2" wire:key="laboratory-search-wrapper">
                        <x-label for="laboratory_id" value="{{ __('Laboratório') }}" class="block text-sm font-medium text-gray-700" />
                        <livewire:profile.profile-search-select
                            model="laboratories"
                            field="laboratory_id"
                            :dependsOn="['institution_id' => $profileData['institution_id']]"
                            placeholder="Digite para buscar laboratório..."
                            wire:model.live.debounce.500ms="profileData.laboratory_id"
                            :initialValue="$profileData['laboratory_id']"
                            :key="'profile-laboratory_id'"
                        />
                        <x-input-error for="profileData.laboratory_id" class="mt-1 text-sm text-red-500" />
                    </div>

                    <div class="flex items-center">
                        <x-label for="showNewLaboratory" class="inline-flex items-center">
                            <input 
                                type="checkbox" 
                                id="showNewLaboratory" 
                                wire:model.live="profileData.showNewLaboratory" 
                                class="form-checkbox h-4 w-4 text-blue-500 border-1 border-gray-300 rounded" 
                            />
                            <span class="ml-2 text-sm text-gray-700">{{ __('Meu laboratório não está na lista') }}</span>
                        </x-label>
                    </div>

                    <div class="{{ !($profileData['showNewInstitution'] || $profileData['showNewLaboratory']) ? 'hidden' : 'block' }} space-y-2">
                        <x-label for="new_laboratory" value="{{ __('Novo Laboratório') }}" class="block text-sm font-medium text-gray-700" />
                        <x-input 
                            id="new_laboratory" 
                            type="text" 
                            class="block w-full" 
                            wire:model.live="profileData.new_laboratory" 
                        />
                        <x-input-error for="profileData.new_laboratory" class="mt-1 text-sm text-red-500" />
                    </div>

                    <div class="flex items-center">
                        <x-label for="lab_coordinator" class="inline-flex items-center">
                            <input 
                                type="checkbox" 
                                id="lab_coordinator" 
                                wire:model.live="profileData.lab_coordinator" 
                                class="form-checkbox h-4 w-4 text-blue-500 border-1 border-gray-300 rounded" 
                            />
                            <span class="ml-2 text-sm text-gray-700">{{ __('Coordenador de Laboratório') }}</span>
                        </x-label>
                    </div>
                </div>
            </div>

            <!-- Categorias de Interesse -->
            <div class="pt-6 border-t border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">{{ __('Categorias de Interesse (opcional)') }}</h3>
                <p class="text-sm text-gray-500 mb-3">{{ __('Selecione até 3 subcategorias de qualquer categoria.') }}</p>

                <div class="space-y-4">
                    <div class="space-y-2">
                        <x-label for="selectedCategory" value="{{ __('Categoria') }}" class="block text-sm font-medium text-gray-700" />
                        <select 
                            id="selectedCategory" 
                            wire:model.live="selectedCategory" 
                            class="block w-full px-4 py-2 border-1 border-gray-300 rounded text-gray-700 focus:ring-2 focus:ring-blue-500 focus:outline-transparent transition-colors"
                        >
                            <option value="">{{ __('Selecione uma categoria') }}</option>
                            @foreach(array_keys($categories) as $category)
                                <option value="{{ $category }}">{{ $category }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="{{ $selectedCategory ? 'block' : 'hidden' }} space-y-2">
                        <x-label for="subcategory-select" value="{{ __('Subcategoria') }}" class="block text-sm font-medium text-gray-700" />
                        <div class="flex gap-2">
                            <select 
                                id="subcategory-select" 
                                class="block w-full px-4 py-2 border-1 border-gray-300 rounded text-gray-700 focus:ring-2 focus:ring-blue-500 focus:outline-transparent transition-colors"
                            >
                                <option value="">{{ __('Selecione uma subcategoria') }}</option>
                                @if($selectedCategory)
                                    @foreach($subcategories as $subcategory)
                                        <option value="{{ $subcategory }}">{{ $subcategory }}</option>
                                    @endforeach
                                @endif
                            </select>
                            <button 
                                type="button" 
                                class="px-4 py-2 bg-blue-500 text-white rounded shadow hover:bg-blue-600 active:bg-blue-700 transition-colors" 
                                x-data 
                                x-on:click="const select = document.getElementById('subcategory-select'); const value = select.options[select.selectedIndex].value; if (value) $wire.addSubcategory(value);"
                            >
                                {{ __('Adicionar') }}
                            </button>
                        </div>
                        <p class="text-xs text-gray-500 mt-1">{{ __('Selecionadas:') }} {{ count($selectedSubcategories) }}/3</p>
                    </div>

                    @if(count($selectedSubcategories) > 0)
                        <div class="p-4 bg-stone-50 rounded shadow-inner">
                            <h4 class="text-sm font-medium text-gray-700 mb-2">{{ __('Subcategorias selecionadas:') }}</h4>
                            <ul class="space-y-2">
                                @foreach($selectedSubcategories as $index => $item)
                                    <li class="flex justify-between items-center p-2 bg-white rounded border-1 border-gray-200">
                                        <span class="text-sm text-gray-700">{{ $item['category'] }} / {{ $item['subcategory'] }}</span>
                                        <button 
                                            type="button" 
                                            class="text-red-500 hover:text-red-700 transition-colors" 
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
            </div>
        </div>
    </x-slot>

    <x-slot name="actions">
        <x-action-message class="me-3 text-green-600" on="saved">
            {{ __('Salvo.') }}
        </x-action-message>

        <x-button 
            wire:loading.attr="disabled" 
            wire:target="photo" 
            class="bg-blue-500 text-white py-2 px-4 rounded shadow hover:bg-blue-600 active:bg-blue-700 transition-colors"
        >
            {{ __('Salvar') }}
        </x-button>
    </x-slot>
</x-form-section>