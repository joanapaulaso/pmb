@extends('admin.layouts.admin-layout')

@section('page-title', 'Adicionar Novo Laboratório')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white shadow rounded-lg overflow-hidden">
        <div class="bg-stone-50 px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-900">Adicionar Novo Laboratório</h2>
        </div>

        <livewire:admin.laboratory-create />
    </div>
</div>
@endsection