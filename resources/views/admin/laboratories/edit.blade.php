@extends('admin.layouts.admin-layout')

@section('page-title', 'Editar Laboratório')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white shadow-md rounded-lg overflow-hidden">
        <div class="bg-gray-50 px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-medium text-gray-900">Informações do Laboratório</h2>
        </div>

        <livewire:admin.admin-laboratory-form :laboratory="$laboratory" />
    </div>

    <div class="mt-6 bg-white shadow-md rounded-lg overflow-hidden">
        <div class="bg-gray-50 px-6 py-4 border-b border-gray-200 flex items-center justify-between">
            <h2 class="text-lg font-medium text-gray-900">Equipe Associada</h2>
            @if($laboratory->team_id)
            <a href="{{ route('teams.show', $laboratory->team_id) }}" class="inline-flex items-center px-3 py-1 border border-transparent text-sm leading-4 font-medium rounded-md text-green-700 bg-green-100 hover:bg-green-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                Gerenciar Equipe
            </a>
            @endif
        </div>

        <div class="p-6">
            @if($laboratory->team_id)
                @php
                    $team = \App\Models\Team::find($laboratory->team_id);
                @endphp

                @if($team)
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-base font-medium text-gray-900">{{ $team->name }}</h3>
                        <p class="text-sm text-gray-500">Proprietário: {{ $team->owner->name ?? 'Desconhecido' }}</p>
                        <p class="text-sm text-gray-500">Membros: {{ $team->users->count() }}</p>
                    </div>
                </div>
                @else
                <p class="text-sm text-gray-500">A equipe associada não foi encontrada ou pode ter sido excluída.</p>
                @endif
            @else
                <p class="text-sm text-gray-500">Este laboratório não tem uma equipe associada no sistema.</p>
            @endif
        </div>
    </div>
</div>
@endsection
