@extends('layouts.guest')

@section('content')
    <x-authentication-card>
        <x-slot name="logo">
            <div class="p-5 shrink-0 flex items-center justify-center">
                <img class="h-20 w-auto" src="{{ Vite::asset('resources/images/logo.png') }}" alt="Logo">
            </div>
        </x-slot>

        <div class="mb-4 text-sm text-gray-600">
            {{ __('Esqueceu sua senha? Sem problema. Basta nos informar seu endereço de e-mail e enviaremos um link de redefinição de senha que permitirá que você escolha uma nova.') }}
        </div>

        @session('status')
            <div class="mb-4 font-medium text-sm text-green-600">
                {{ $value }}
            </div>
        @endsession

        <x-validation-errors class="mb-4" />

        <form method="POST" action="{{ route('password.email') }}">
            @csrf

            <div class="block">
                <x-label for="email" value="{{ __('E-mail') }}" />
                <x-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
            </div>

            <div class="flex items-center justify-end mt-4">
                <x-button>
                    {{ __('Enviar Link de Redefinição de Senha por E-mail') }}
                </x-button>
            </div>
        </form>
    </x-authentication-card>
@endsection