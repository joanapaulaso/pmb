@extends('layouts.guest')

@section('content')
    <div class="min-h-screen flex items-center justify-center bg-gray-50 py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full p-8 bg-white rounded-lg shadow-md">
            <h2 class="text-center text-2xl font-bold text-[#267e90] mb-6">Criar uma Conta</h2>

            @if (session('message'))
                <div class="p-4 mb-4 bg-green-100 text-green-700 rounded-lg">
                    {{ session('message') }}
                </div>
            @endif

            <form method="POST" action="{{ route('register') }}" class="space-y-6">
                @csrf

                <div>
                    <label class="block text-sm font-medium text-gray-700">Nome Completo</label>
                    <input type="text" name="full_name" required class="w-full px-4 py-2 border rounded-md">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Email</label>
                    <input type="email" name="email" required class="w-full px-4 py-2 border rounded-md">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Senha</label>
                    <input type="password" name="password" required class="w-full px-4 py-2 border rounded-md">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Confirmar Senha</label>
                    <input type="password" name="password_confirmation" required class="w-full px-4 py-2 border rounded-md">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Data de Nascimento</label>
                    <input type="date" name="birth_date" required class="w-full px-4 py-2 border rounded-md">
                </div>

                <!-- Checkbox para indicar se o usuário é de fora do Brasil -->
                <div class="mb-4">
                    <label class="inline-flex items-center">
                        <input type="checkbox" id="isInternational" name="isInternational" class="form-checkbox">
                        <span class="ml-2 text-sm">Não sou do Brasil</span>
                    </label>
                </div>

                <!-- Campos para brasileiros -->
                <div id="brazilianFields">
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">Estado</label>
                        <input type="text" name="state" class="w-full px-4 py-2 border rounded-md">
                        <!-- Estado -->
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">Município</label>
                        <input type="text" name="municipality" class="w-full px-4 py-2 border rounded-md">
                    </div>

                    <input type="hidden" name="country_code" value="BR"> <!-- Define Brasil automaticamente -->
                </div>

                <!-- Campos para estrangeiros -->
                <div id="internationalFields" class="hidden">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">País</label>
                        <select name="country_code" id="country" class="w-full px-4 py-2 border rounded-md"></select>
                    </div>
                </div>

                <!-- Campos de Instituição -->
                <div id="institutionFields" class="mb-4">
                    <label class="block text-sm font-medium text-gray-700">Instituição</label>
                    <input type="text" name="institution" class="w-full px-4 py-2 border rounded-md">
                    <!-- Instituição que depende do estado -->
                </div>

                <!-- Checkbox para adicionar nova instituição -->
                <div class="mb-4">
                    <label class="inline-flex items-center">
                        <input type="checkbox" id="newInstitutionCheckbox" class="form-checkbox">
                        <span class="ml-2 text-sm">Minha instituição não está na lista</span>
                    </label>
                </div>

                <!-- Campo para nova instituição -->
                <div id="newInstitutionField" class="hidden">
                    <label class="block text-sm font-medium text-gray-700">Nova Instituição</label>
                    <input type="text" name="new_institution" class="w-full px-4 py-2 border rounded-md">
                </div>

                <!-- Campos de Laboratório -->
                <div id="laboratoryFields" class="mb-4">
                    <label class="block text-sm font-medium text-gray-700">Laboratório</label>
                    <input type="text" name="laboratory" class="w-full px-4 py-2 border rounded-md">
                    <!-- Laboratório que depende da instituição -->
                </div>

                <!-- Checkbox para adicionar novo laboratório -->
                <div class="mb-4">
                    <label class="inline-flex items-center">
                        <input type="checkbox" id="newLaboratoryCheckbox" class="form-checkbox">
                        <span class="ml-2 text-sm">Meu laboratório não está na lista</span>
                    </label>
                </div>

                <!-- Campo para novo laboratório -->
                <div id="newLaboratoryField" class="hidden">
                    <label class="block text-sm font-medium text-gray-700">Novo Laboratório</label>
                    <input type="text" name="new_laboratory" class="w-full px-4 py-2 border rounded-md">
                </div>

                <div>
                    <label class="inline-flex items-center">
                        <input type="checkbox" name="lab_coordinator" class="form-checkbox">
                        <span class="ml-2">Coordenador de Laboratório?</span>
                    </label>
                </div>

                <div>
                    <button type="submit" class="w-full bg-blue-500 text-white py-2 px-4 rounded-md shadow">
                        Registrar
                    </button>
                </div>
            </form>
        </div>
    </div>

@endsection
