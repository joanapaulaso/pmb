@extends('admin.layouts.admin-layout')

@section('page-title', 'Adicionar Novo Laboratório')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white shadow-md rounded-lg overflow-hidden">
        <div class="bg-gray-50 px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-medium text-gray-900">Informações do Laboratório</h2>
        </div>

        <livewire:admin.admin-laboratory-form />
    </div>
</div>
@endsection
