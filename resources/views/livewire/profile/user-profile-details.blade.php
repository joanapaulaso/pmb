<x-action-section>
    <x-slot name="title">
        {{ __('Informações do Perfil') }}
    </x-slot>

    <x-slot name="description">
        {{ __('Visualize suas informações de perfil, localização e área de interesse.') }}
    </x-slot>

    <x-slot name="content">
        <div class="space-y-6">
            <!-- Informações Pessoais -->
            <div>
                <h3 class="text-lg font-medium text-gray-900">{{ __('Informações Pessoais') }}</h3>

                <div class="mt-3 grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <p class="text-sm text-gray-500">{{ __('Nome') }}</p>
                        <p class="text-base font-medium text-gray-900">{{ $user->name }}</p>
                    </div>

                    <div>
                        <p class="text-sm text-gray-500">{{ __('Email') }}</p>
                        <p class="text-base font-medium text-gray-900">{{ $user->email }}</p>
                    </div>

                    <div>
                        <p class="text-sm text-gray-500">{{ __('Data de Nascimento') }}</p>
                        <p class="text-base font-medium text-gray-900">
                            {{ $profile->birth_date ? \Carbon\Carbon::parse($profile->birth_date)->format('d/m/Y') : __('Não informado') }}
                        </p>
                    </div>

                    <div>
                        <p class="text-sm text-gray-500">{{ __('Gênero') }}</p>
                        <p class="text-base font-medium text-gray-900">
                            @if($profile->gender)
                                @if($profile->gender == 'feminino')
                                    {{ __('Feminino') }}
                                @elseif($profile->gender == 'masculino')
                                    {{ __('Masculino') }}
                                @elseif($profile->gender == 'nao-binario')
                                    {{ __('Não-binário') }}
                                @elseif($profile->gender == 'outro')
                                    {{ __('Outro') }}
                                @elseif($profile->gender == 'prefiro-nao-dizer')
                                    {{ __('Prefiro não dizer') }}
                                @else
                                    {{ $profile->gender }}
                                @endif
                            @else
                                {{ __('Não informado') }}
                            @endif
                        </p>
                    </div>
                </div>
            </div>

            <!-- Localização -->
            <div class="pt-6 border-t border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">{{ __('Localização') }}</h3>

                <div class="mt-3 grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <p class="text-sm text-gray-500">{{ __('País') }}</p>
                        <p class="text-base font-medium text-gray-900">
                            {{ $country ? $country->name : __('Não informado') }}
                        </p>
                    </div>

                    @if(!$profile->country_code || $profile->country_code == 'BR')
                        <div>
                            <p class="text-sm text-gray-500">{{ __('Estado') }}</p>
                            <p class="text-base font-medium text-gray-900">
                                {{ $state ? $state->name : __('Não informado') }}
                            </p>
                        </div>

                        <div>
                            <p class="text-sm text-gray-500">{{ __('Município') }}</p>
                            <p class="text-base font-medium text-gray-900">
                                {{ $municipality ? $municipality->name : __('Não informado') }}
                            </p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Instituição e Laboratório -->
            <div class="pt-6 border-t border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">{{ __('Instituição e Laboratório') }}</h3>

                <div class="mt-3 grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <p class="text-sm text-gray-500">{{ __('Instituição') }}</p>
                        <p class="text-base font-medium text-gray-900">
                            {{ $institution ? $institution->name : __('Não informado') }}
                        </p>
                    </div>

                    <div>
                        <p class="text-sm text-gray-500">{{ __('Laboratório') }}</p>
                        <p class="text-base font-medium text-gray-900">
                            {{ $laboratory ? $laboratory->name : __('Não informado') }}
                        </p>
                    </div>

                    <div>
                        <p class="text-sm text-gray-500">{{ __('Coordenador de Laboratório') }}</p>
                        <p class="text-base font-medium text-gray-900">
                            {{ $profile->lab_coordinator ? __('Sim') : __('Não') }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- Categorias de Interesse -->
            <div class="pt-6 border-t border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">{{ __('Categorias de Interesse') }}</h3>

                <div class="mt-3">
                    @if(count($userCategories) > 0)
                        <ul class="space-y-2">
                            @foreach($userCategories as $category)
                                <li class="bg-gray-50 p-3 rounded-md">
                                    <span class="font-medium">{{ $category->category_name }}</span> / {{ $category->subcategory_name }}
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <p class="text-gray-500">{{ __('Nenhuma categoria de interesse selecionada.') }}</p>
                    @endif
                </div>
            </div>
        </div>
    </x-slot>
</x-action-section>
