<x-guest-layout>
    <x-authentication-card>
        <x-slot name="logo">
            <img class="h-20 w-auto" src="{{ Vite::asset('resources/images/logo.png') }}" alt="Logo">
        </x-slot>

        <h2 class="text-center text-2xl font-bold text-[#267e90] mb-6">Criar uma Conta</h2>

        <x-validation-errors class="mb-4" />

        @if (session('message'))
            <div class="mb-4 font-medium text-sm text-green-600">
                {{ session('message') }}
            </div>
        @endif

        <form method="POST" action="{{ route('register') }}">
            @csrf

            <div>
                <x-label for="full_name" value="{{ __('Nome Completo') }}" />
                <x-input id="full_name" class="block mt-1 w-full" type="text" name="full_name" :value="old('full_name')" required autofocus />
            </div>

            <div class="mt-4">
                <x-label for="email" value="{{ __('Email') }}" />
                <x-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required />
            </div>

            <div class="mt-4">
                <x-label for="password" value="{{ __('Senha') }}" />
                <x-input id="password" class="block mt-1 w-full" type="password" name="password" required autocomplete="new-password" />
            </div>

            <div class="mt-4">
                <x-label for="password_confirmation" value="{{ __('Confirmar Senha') }}" />
                <x-input id="password_confirmation" class="block mt-1 w-full" type="password" name="password_confirmation" required autocomplete="new-password" />
            </div>

            <div class="mt-4">
                <x-label for="birth_date" value="{{ __('Data de Nascimento') }}" />
                <x-input id="birth_date" class="block mt-1 w-full" type="date" name="birth_date" :value="old('birth_date')" required />
            </div>

            <div class="mt-4">
                <label class="flex items-center">
                    <x-checkbox id="isInternational" name="isInternational" />
                    <span class="ms-2 text-sm text-gray-600">{{ __('Não sou do Brasil') }}</span>
                </label>
            </div>

            <!-- Campos para brasileiros -->
            <div id="brazilianFields" class="mt-4">
                <div>
                    <x-label for="state" value="{{ __('Estado') }}" />
                    <x-input id="state" class="block mt-1 w-full" type="text" name="state" :value="old('state')" />
                </div>

                <div class="mt-4">
                    <x-label for="municipality" value="{{ __('Município') }}" />
                    <x-input id="municipality" class="block mt-1 w-full" type="text" name="municipality" :value="old('municipality')" />
                </div>

                <input type="hidden" name="country_code" value="BR">
            </div>

            <!-- Campos para estrangeiros -->
            <div id="internationalFields" class="hidden mt-4">
                <div>
                    <x-label for="country" value="{{ __('País') }}" />
                    <select name="country_code" id="country" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm block mt-1 w-full"></select>
                </div>
            </div>

            <!-- Campos de Instituição -->
            <div id="institutionFields" class="mt-4">
                <x-label for="institution" value="{{ __('Instituição') }}" />
                <x-input id="institution" class="block mt-1 w-full" type="text" name="institution" :value="old('institution')" />
            </div>

            <!-- Checkbox para adicionar nova instituição -->
            <div class="mt-4">
                <label class="flex items-center">
                    <x-checkbox id="newInstitutionCheckbox" />
                    <span class="ms-2 text-sm text-gray-600">{{ __('Minha instituição não está na lista') }}</span>
                </label>
            </div>

            <!-- Campo para nova instituição -->
            <div id="newInstitutionField" class="hidden mt-4">
                <x-label for="new_institution" value="{{ __('Nova Instituição') }}" />
                <x-input id="new_institution" class="block mt-1 w-full" type="text" name="new_institution" :value="old('new_institution')" />
            </div>

            <!-- Campos de Laboratório -->
            <div id="laboratoryFields" class="mt-4">
                <x-label for="laboratory" value="{{ __('Laboratório') }}" />
                <x-input id="laboratory" class="block mt-1 w-full" type="text" name="laboratory" :value="old('laboratory')" />
            </div>

            <!-- Checkbox para adicionar novo laboratório -->
            <div class="mt-4">
                <label class="flex items-center">
                    <x-checkbox id="newLaboratoryCheckbox" />
                    <span class="ms-2 text-sm text-gray-600">{{ __('Meu laboratório não está na lista') }}</span>
                </label>
            </div>

            <!-- Campo para novo laboratório -->
            <div id="newLaboratoryField" class="hidden mt-4">
                <x-label for="new_laboratory" value="{{ __('Novo Laboratório') }}" />
                <x-input id="new_laboratory" class="block mt-1 w-full" type="text" name="new_laboratory" :value="old('new_laboratory')" />
            </div>

            <div class="mt-4">
                <label class="flex items-center">
                    <x-checkbox id="lab_coordinator" name="lab_coordinator" />
                    <span class="ms-2 text-sm text-gray-600">{{ __('Coordenador de Laboratório?') }}</span>
                </label>
            </div>

            <div class="flex items-center justify-end mt-4">
                <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" href="{{ route('login') }}">
                    {{ __('Já possui uma conta?') }}
                </a>

                <x-button class="ms-4">
                    {{ __('Registrar') }}
                </x-button>
            </div>
        </form>
    </x-authentication-card>
</x-guest-layout>
