<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Membros UPDATE') }}
        </h2>
    </x-slot>

    <x-members-list :users="$users" />
</x-app-layout>
