@extends('admin.layouts.admin-layout')

@section('page-title', 'Adicionar Novo Evento')

@section('content')
<div class="max-w-5xl mx-auto">
    <form action="{{ route('admin.events.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="bg-white shadow rounded-lg overflow-hidden mb-6">
            <div class="bg-stone-50 px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-900">Informações do Evento</h2>
            </div>

            <div class="p-6 space-y-6">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <div>
                        <label for="title" class="block text-sm font-medium text-gray-700 mb-1">Título do Evento <span class="text-red-500">*</span></label>
                        <input 
                            type="text" 
                            name="title" 
                            id="title" 
                            value="{{ old('title') }}" 
                            class="block w-full px-4 py-2 border-1 border-gray-300 rounded text-gray-700 focus:ring-2 focus:ring-blue-500 focus:outline-transparent transition-colors" 
                            required
                        >
                        @error('title')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="event_type" class="block text-sm font-medium text-gray-700 mb-1">Tipo de Evento <span class="text-red-500">*</span></label>
                        <select 
                            name="event_type" 
                            id="event_type" 
                            class="block w-full px-4 py-2 border-1 border-gray-300 rounded text-gray-700 focus:ring-2 focus:ring-blue-500 focus:outline-transparent transition-colors" 
                            required
                        >
                            <option value="seminar" {{ old('event_type') == 'seminar' ? 'selected' : '' }}>Seminário</option>
                            <option value="workshop" {{ old('event_type') == 'workshop' ? 'selected' : '' }}>Workshop</option>
                            <option value="conference" {{ old('event_type') == 'conference' ? 'selected' : '' }}>Conferência</option>
                            <option value="webinar" {{ old('event_type') == 'webinar' ? 'selected' : '' }}>Webinário</option>
                        </select>
                        @error('event_type')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Descrição <span class="text-red-500">*</span></label>
                    <textarea 
                        name="description" 
                        id="description" 
                        rows="4" 
                        class="block w-full px-4 py-2 border-1 border-gray-300 rounded text-gray-700 focus:ring-2 focus:ring-blue-500 focus:outline-transparent transition-colors" 
                        required
                    >{{ old('description') }}</textarea>
                    @error('description')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <div>
                        <label for="start_date" class="block text-sm font-medium text-gray-700 mb-1">Data de Início <span class="text-red-500">*</span></label>
                        <input 
                            type="datetime-local" 
                            name="start_date" 
                            id="start_date" 
                            value="{{ old('start_date') }}" 
                            class="block w-full px-4 py-2 border-1 border-gray-300 rounded text-gray-700 focus:ring-2 focus:ring-blue-500 focus:outline-transparent transition-colors" 
                            required
                        >
                        @error('start_date')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="end_date" class="block text-sm font-medium text-gray-700 mb-1">Data de Término <span class="text-red-500">*</span></label>
                        <input 
                            type="datetime-local" 
                            name="end_date" 
                            id="end_date" 
                            value="{{ old('end_date') }}" 
                            class="block w-full px-4 py-2 border-1 border-gray-300 rounded text-gray-700 focus:ring-2 focus:ring-blue-500 focus:outline-transparent transition-colors" 
                            required
                        >
                        @error('end_date')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <div>
                        <label for="location" class="block text-sm font-medium text-gray-700 mb-1">Local (opcional)</label>
                        <input 
                            type="text" 
                            name="location" 
                            id="location" 
                            value="{{ old('location') }}" 
                            class="block w-full px-4 py-2 border-1 border-gray-300 rounded text-gray-700 focus:ring-2 focus:ring-blue-500 focus:outline-transparent transition-colors"
                        >
                        @error('location')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="online_url" class="block text-sm font-medium text-gray-700 mb-1">URL Online (opcional)</label>
                        <input 
                            type="url" 
                            name="online_url" 
                            id="online_url" 
                            value="{{ old('online_url') }}" 
                            placeholder="https://meet.google.com/..." 
                            class="block w-full px-4 py-2 border-1 border-gray-300 rounded text-gray-700 focus:ring-2 focus:ring-blue-500 focus:outline-transparent transition-colors"
                        >
                        @error('online_url')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div>
                    <label for="registration_url" class="block text-sm font-medium text-gray-700 mb-1">URL para Inscrição (opcional)</label>
                    <input 
                        type="url" 
                        name="registration_url" 
                        id="registration_url" 
                        value="{{ old('registration_url') }}" 
                        placeholder="https://forms.google.com/..." 
                        class="block w-full px-4 py-2 border-1 border-gray-300 rounded text-gray-700 focus:ring-2 focus:ring-blue-500 focus:outline-transparent transition-colors"
                    >
                    @error('registration_url')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="image" class="block text-sm font-medium text-gray-700 mb-1">Imagem do Evento (opcional)</label>
                    <input 
                        type="file" 
                        name="image" 
                        id="image" 
                        class="block w-full text-sm text-gray-700 file:mr-4 file:py-2 file:px-4 file:rounded file:border-0 file:bg-blue-500 file:text-white hover:file:bg-blue-600 transition-colors" 
                        accept="image/*"
                    >
                    <p class="mt-1 text-xs text-gray-500">Formatos aceitos: JPG, PNG, GIF. Tamanho máximo: 2MB.</p>
                    @error('image')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <input 
                            type="checkbox" 
                            name="is_published" 
                            id="is_published" 
                            value="1" 
                            {{ old('is_published', true) ? 'checked' : '' }} 
                            class="form-checkbox h-4 w-4 text-blue-500 border-1 border-gray-300 rounded"
                        >
                        <label for="is_published" class="ml-2 block text-sm text-gray-700">Publicar imediatamente</label>
                    </div>

                    <div class="flex items-center">
                        <input 
                            type="checkbox" 
                            name="is_featured" 
                            id="is_featured" 
                            value="1" 
                            {{ old('is_featured') ? 'checked' : '' }} 
                            class="form-checkbox h-4 w-4 text-blue-500 border-1 border-gray-300 rounded"
                        >
                        <label for="is_featured" class="ml-2 block text-sm text-gray-700">Destacar evento</label>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white shadow rounded-lg overflow-hidden mb-6">
            <div class="bg-stone-50 px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                <h2 class="text-lg font-semibold text-gray-900">Palestrantes</h2>
                <button 
                    type="button" 
                    id="add-speaker" 
                    class="inline-flex items-center px-3 py-1 text-sm font-medium text-blue-700 bg-blue-100 rounded shadow hover:bg-blue-200 transition-colors"
                >
                    <svg class="mr-1 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    Adicionar Palestrante
                </button>
            </div>

            <div id="speakers-container" class="p-6 space-y-6">
                <div class="text-sm text-gray-500 text-center py-4" id="no-speakers-message">
                    Nenhum palestrante adicionado. Clique no botão acima para adicionar palestrantes ao evento.
                </div>
            </div>
        </div>

        <div class="flex items-center justify-end gap-3">
            <a 
                href="{{ route('admin.events.index') }}" 
                class="inline-flex items-center px-4 py-2 text-sm text-gray-700 bg-gray-200 rounded shadow hover:bg-gray-300 transition-colors"
            >
                Cancelar
            </a>

            <button 
                type="submit" 
                class="inline-flex items-center px-4 py-2 text-sm text-white bg-blue-500 rounded shadow hover:bg-blue-600 active:bg-blue-700 transition-colors"
            >
                Salvar Evento
            </button>
        </div>
    </form>
</div>

<!-- Template para novos palestrantes -->
<template id="speaker-template">
    <div class="speaker-card bg-stone-50 rounded-lg p-4 relative shadow-xs">
        <button type="button" class="remove-speaker absolute top-2 right-2 text-gray-400 hover:text-red-500 transition-colors">
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </button>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nome do Palestrante <span class="text-red-500">*</span></label>
                <input 
                    type="text" 
                    name="speakers[0][name]" 
                    class="speaker-name block w-full px-4 py-2 border-1 border-gray-300 rounded text-gray-700 focus:ring-2 focus:ring-blue-500 focus:outline-transparent transition-colors" 
                    required
                >
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Instituição</label>
                    <input 
                        type="text" 
                        name="speakers[0][institution]" 
                        class="block w-full px-4 py-2 border-1 border-gray-300 rounded text-gray-700 focus:ring-2 focus:ring-blue-500 focus:outline-transparent transition-colors"
                    >
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Cargo/Função</label>
                    <input 
                        type="text" 
                        name="speakers[0][role]" 
                        class="block w-full px-4 py-2 border-1 border-gray-300 rounded text-gray-700 focus:ring-2 focus:ring-blue-500 focus:outline-transparent transition-colors"
                    >
                </div>
            </div>
        </div>

        <div class="mt-4">
            <label class="block text-sm font-medium text-gray-700 mb-1">Mini Biografia</label>
            <textarea 
                name="speakers[0][bio]" 
                rows="2" 
                class="block w-full px-4 py-2 border-1 border-gray-300 rounded text-gray-700 focus:ring-2 focus:ring-blue-500 focus:outline-transparent transition-colors"
            ></textarea>
        </div>

        <div class="mt-4">
            <label class="block text-sm font-medium text-gray-700 mb-1">Foto (opcional)</label>
            <input 
                type="file" 
                name="speakers[0][photo]" 
                class="block w-full text-sm text-gray-700 file:mr-4 file:py-2 file:px-4 file:rounded file:border-0 file:bg-blue-500 file:text-white hover:file:bg-blue-600 transition-colors" 
                accept="image/*"
            >
        </div>
    </div>
</template>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const speakersContainer = document.getElementById('speakers-container');
        const addSpeakerButton = document.getElementById('add-speaker');
        const speakerTemplate = document.getElementById('speaker-template');
        const noSpeakersMessage = document.getElementById('no-speakers-message');
        let speakerCount = 0;

        // Função para adicionar um novo palestrante
        function addSpeaker() {
            // Esconder a mensagem de nenhum palestrante
            noSpeakersMessage.style.display = 'none';

            // Clonar o template
            const newSpeaker = speakerTemplate.content.cloneNode(true);

            // Atualizar os índices dos campos
            const speakerIndex = speakerCount;
            const inputs = newSpeaker.querySelectorAll('input, textarea');
            inputs.forEach(input => {
                if (input.name) {
                    input.name = input.name.replace('[0]', `[${speakerIndex}]`);
                }
            });

            // Adicionar evento para remover palestrante
            const removeButton = newSpeaker.querySelector('.remove-speaker');
            removeButton.addEventListener('click', function() {
                this.closest('.speaker-card').remove();

                // Se não houver mais palestrantes, mostrar mensagem
                if (speakersContainer.querySelectorAll('.speaker-card').length === 0) {
                    noSpeakersMessage.style.display = 'block';
                }
            });

            // Adicionar o novo palestrante ao container
            speakersContainer.appendChild(newSpeaker);

            // Incrementar o contador de palestrantes
            speakerCount++;
        }

        // Adicionar evento para o botão de adicionar palestrante
        addSpeakerButton.addEventListener('click', addSpeaker);

        // Adicionar pelo menos um palestrante por padrão
        addSpeaker();
    });
</script>
@endpush
@endsection