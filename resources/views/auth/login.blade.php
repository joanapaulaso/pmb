@extends('layouts.guest')

@section('content')
    <x-authentication-card>
        <x-slot name="logo">
            <div class="p-5 shrink-0 flex items-center justify-center">
                <img class="h-20 w-auto" src="{{ Vite::asset('resources/images/logo.png') }}" alt="Logo">
            </div>
        </x-slot>

        <x-validation-errors class="mb-4 text-red-500 text-sm" />

        @if (session('status'))
            <div class="mb-4 font-medium text-sm text-green-600">
                {{ session('status') }}
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}" class="space-y-6">
            @csrf

            <div>
                <x-label for="email" value="{{ __('Email') }}" class="block text-sm font-medium text-gray-700" />
                <x-input 
                    id="email" 
                    class="block mt-1 w-full px-4 py-2 border-1 border-gray-300 rounded text-gray-700 focus:ring-2 focus:ring-blue-500 focus:outline-transparent transition-colors" 
                    type="email" 
                    name="email" 
                    :value="old('email')" 
                    required 
                    autofocus 
                    autocomplete="username" 
                />
            </div>

            <div class="mt-4">
                <x-label for="password" value="{{ __('Password') }}" class="block text-sm font-medium text-gray-700" />
                <x-input 
                    id="password" 
                    class="block mt-1 w-full px-4 py-2 border-1 border-gray-300 rounded text-gray-700 focus:ring-2 focus:ring-blue-500 focus:outline-transparent transition-colors" 
                    type="password" 
                    name="password" 
                    required 
                    autocomplete="current-password" 
                />
            </div>

            <div class="block mt-4">
                <label for="remember_me" class="flex items-center">
                    <x-checkbox id="remember_me" name="remember" class="form-checkbox text-blue-500" />
                    <span class="ms-2 text-sm text-gray-600">{{ __('Lembrar meus dados') }}</span>
                </label>
            </div>

            <div class="flex items-center justify-end mt-4 gap-4">
                @if (Route::has('password.request'))
                    <a 
                        class="underline text-sm text-gray-600 hover:text-gray-900 focus:ring-2 focus:ring-blue-500 focus:outline-transparent transition-colors" 
                        href="{{ route('password.request') }}"
                    >
                        {{ __('Esqueceu a senha?') }}
                    </a>
                @endif

                <x-button class="bg-blue-500 text-white py-2 px-4 rounded shadow hover:bg-blue-600 active:bg-blue-700 transition-colors">
                    {{ __('Log in') }}
                </x-button>
            </div>
        </form>

        <div class="mt-6 text-center">
            <p class="text-sm text-gray-600">
                {{ __('Ainda não se registrou?') }}
                <a href="{{ route('register') }}" class="font-medium text-blue-600 hover:text-blue-700 transition-colors">
                    {{ __('Registre-se!') }}
                </a>
            </p>
        </div>
    </x-authentication-card>
@endsection