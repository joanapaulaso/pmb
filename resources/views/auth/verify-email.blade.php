@extends('layouts.guest')

@section('content')
    <x-authentication-card>
        <x-slot name="logo">
            <div class="p-5 shrink-0 flex items-center justify-center">
                <img class="h-20 w-auto" src="{{ Vite::asset('resources/images/logo.png') }}" alt="Logo">
            </div>
        </x-slot>

        <div class="mb-4 text-sm text-gray-600">
            {{ __('Para continuar, clique no botão abaixo solicitar a verificação do seu e-mail. Instruções serão enviadas ao e-mail cadastrado.') }}
        </div>

        @if (session('status') == 'verification-link-sent')
            <div class="mb-4 font-medium text-sm text-green-600">
                {{ __('Um link de verificação foi enviado para o endereço de e-mail que você forneceu nas configurações do seu perfil.') }}
            </div>
        @endif

        <div class="mt-4 flex items-center justify-between">
            <form method="POST" action="{{ route('verification.send') }}">
                @csrf

                <div>
                    <x-button type="submit">
                        {{ __('Enviar E-mail de Verificação') }}
                    </x-button>
                </div>
            </form>

            <div>
                {{-- <a
                    href="{{ route('profile.show') }}" class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 ms-2"
                >
                    {{ __('Editar Perfil') }}</a> --}}

                <form method="POST" action="{{ route('logout') }}" class="inline">
                    @csrf

                    <button type="submit" class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 ms-2">
                        {{ __('Desconectar') }}
                    </button>
                </form>
            </div>
        </div>
    </x-authentication-card>
@endsection