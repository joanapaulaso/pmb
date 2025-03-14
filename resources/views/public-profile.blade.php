<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Perfil Público') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <x-action-section>
                        <x-slot name="title">
                            {{ __('Informações Detalhadas') }}
                        </x-slot>

                        <x-slot name="description">
                            {{ __('Visualize informações de perfil, localização e área de interesse.') }}
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
                                                @if($profile && $profile->birth_date)
                                                    {{ \Carbon\Carbon::parse($profile->birth_date)->format('d/m/Y') }}
                                                @else
                                                    {{ __('Não informado') }}
                                                @endif
                                            </p>
                                        </div>

                                        <div>
                                            <p class="text-sm text-gray-500">{{ __('Gênero') }}</p>
                                            <p class="text-base font-medium text-gray-900">
                                                @if($profile && $profile->gender)
                                                    @php
                                                        $genderMap = [
                                                            'feminino' => 'Feminino',
                                                            'masculino' => 'Masculino',
                                                            'nao-binario' => 'Não-binário',
                                                            'outro' => 'Outro',
                                                            'prefiro-nao-dizer' => 'Prefiro não dizer'
                                                        ];
                                                    @endphp
                                                    {{ $genderMap[$profile->gender] ?? $profile->gender }}
                                                @else
                                                    {{ __('Não informado') }}
                                                @endif
                                            </p>
                                        </div>

                                        <div>
                                            <p class="text-sm text-gray-500">{{ __('Status') }}</p>
                                            <p class="text-base font-medium text-gray-900">
                                                {{ $user->admin ? __('Administrador') : __('Usuário') }}
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

                                        @if(!$profile || !$profile->country_code || $profile->country_code == 'BR')
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
                                            <p class="text-sm text-gray-500">{{ __('Nome do Laboratório (Sistema)') }}</p>
                                            <p class="text-base font-medium text-gray-900">
                                                @if($labTeam)
                                                    {{ $labTeam->name }}
                                                @elseif($laboratory && $laboratory->team_id && isset($teams))
                                                    @php
                                                        $foundTeam = null;
                                                        foreach ($teams as $team) {
                                                            if ($team->id == $laboratory->team_id) {
                                                                $foundTeam = $team;
                                                                break;
                                                            }
                                                        }
                                                    @endphp
                                                    {{ $foundTeam ? $foundTeam->name : __('Não informado') }}
                                                @else
                                                    {{ __('Não informado') }}
                                                @endif
                                            </p>
                                        </div>

                                        <div>
                                            <p class="text-sm text-gray-500">{{ __('Coordenador de Laboratório') }}</p>
                                            <p class="text-base font-medium text-gray-900">
                                                {{ ($profile && $profile->lab_coordinator) ? __('Sim') : __('Não') }}
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Equipes/Teams -->
                                <div class="pt-6 border-t border-gray-200">
                                    <h3 class="text-lg font-medium text-gray-900">{{ __('Laboratórios/Equipes') }}</h3>

                                    <div class="mt-3">
                                        @if(isset($teams) && count($teams) > 0)
                                            <ul class="space-y-2">
                                                @foreach($teams as $team)
                                                    <li class="bg-gray-50 p-3 rounded-md flex justify-between items-center">
                                                        <div>
                                                            <span class="font-medium">{{ $team->name }}</span>
                                                            @if($team->user_id === $user->id)
                                                                <span class="ml-2 px-2 py-0.5 bg-blue-100 text-blue-800 text-xs rounded-full">{{ __('Coordenador') }}</span>
                                                            @endif
                                                        </div>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        @else
                                            <p class="text-gray-500">{{ __('Nenhum laboratório/equipe associado.') }}</p>
                                        @endif
                                    </div>
                                </div>

                                <!-- Categorias de Interesse -->
                                <div class="pt-6 border-t border-gray-200">
                                    <h3 class="text-lg font-medium text-gray-900">{{ __('Categorias de Interesse') }}</h3>

                                    <div class="mt-3">
                                        @if(isset($userCategories) && count($userCategories) > 0)
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
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
