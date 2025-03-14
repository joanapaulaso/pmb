@extends('admin.layouts.admin-layout')

@section('page-title', 'Adicionar Novo Evento')

@section('content')
<div class="max-w-5xl mx-auto">
    <form action="{{ route('admin.events.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="bg-white shadow-md rounded-lg overflow-hidden mb-6">
            <div class="bg-gray-50 px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-medium text-gray-900">Informações do Evento</h2>
            </div>

            <div class="p-6 space-y-6">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <div>
                        <label for="title" class="block text-sm font-medium text-gray-700 mb-1">Título do Evento <span class="text-red-500">*</span></label>
                        <input type="text" name="title" id="title" value="{{ old('title') }}" class="shadow-sm focus:ring-purple-500 focus:border-purple-500 block w-full sm:text-sm border-gray-300 rounded-md" required>
                        @error('title')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="event_type" class="block text-sm font-medium text-gray-700 mb-1">Tipo de Evento <span class="text-red-500">*</span></label>
                        <select name="event_type" id="event_type" class="shadow-sm focus:ring-purple-500 focus:border-purple-500 block w-full sm:text-sm border-gray-300 rounded-md" required>
                            <option value="seminar" {{ old('event_type') == 'seminar' ? 'selected' : '' }}>Seminário</option>
                            <option value="workshop" {{ old('event_type') == 'workshop' ? 'selected' : '' }}>Workshop</option>
                            <option value="conference" {{ old('event_type') == 'conference' ? 'selected' : '' }}>Conferência</option>
                            <option value="webinar" {{ old('event_type') == 'webinar' ? 'selected' : '' }}>Webinário</option>
                        </select>
                        @error('event_type')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Descrição <span class="text-red-500">*</span></label>
                    <textarea name="description" id="description" rows="4" class="shadow-sm focus:ring-purple-500 focus:border-purple-500 block w-full sm:text-sm border-gray-300 rounded-md" required>{{ old('description') }}</textarea>
                    @error('description')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <div>
                        <label for="start_date" class="block text-sm font-medium text-gray-700 mb-1">Data de Início <span class="text-red-500">*</span></label>
                        <input type="datetime-local" name="start_date" id="start_date" value="{{ old('start_date') }}" class="shadow-sm focus:ring-purple-500 focus:border-purple-500 block w-full sm:text-sm border-gray-300 rounded-md" required>
                        @error('start_date')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="end_date" class="block text-sm font-medium text-gray-700 mb-1">Data de Término <span class="text-red-500">*</span></label>
                        <input type="datetime-local" name="end_date" id="end_date" value="{{ old('end_date') }}" class="shadow-sm focus:ring-purple-500 focus:border-purple-500 block w-full sm:text-sm border-gray-300 rounded-md" required>
                        @error('end_date')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <div>
                        <label for="location" class="block text-sm font-medium text-gray-700 mb-1">Local (opcional)</label>
                        <input type="text" name="location" id="location" value="{{ old('location') }}" class="shadow-sm focus:ring-purple-500 focus:border-purple-500 block w-full sm:text-sm border-gray-300 rounded-md">
                        @error('location')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="online_url" class="block text-sm font-medium text-gray-700 mb-1">URL Online (opcional)</label>
                        <input type="url" name="online_url" id="online_url" value="{{ old('online_url') }}" placeholder="https://meet.google.com/..." class="shadow-sm focus:ring-purple-500 focus:border-purple-500 block w-full sm:text-sm border-gray-300 rounded-md">
                        @error('online_url')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div>
                    <label for="registration_url" class="block text-sm font-medium text-gray-700 mb-1">URL para Inscrição (opcional)</label>
                    <input type="url" name="registration_url" id="registration_url" value="{{ old('registration_url') }}" placeholder="https://forms.google.com/..." class="shadow-sm focus:ring-purple-500 focus:border-purple-500 block w-full sm:text-sm border-gray-300 rounded-md">
                    @error('registration_url')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="image" class="block text-sm font-medium text-gray-700 mb-1">Imagem do Evento (opcional)</label>
                    <input type="file" name="image" id="image" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-purple-50 file:text-purple-700 hover:file:bg-purple-100" accept="image/*">
                    <p class="mt-1 text-xs text-gray-500">Formatos aceitos: JPG, PNG, GIF. Tamanho máximo: 2MB.</p>
                    @error('image')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <input type="checkbox" name="is_published" id="is_published" value="1" {{ old('is_published', true) ? 'checked' : '' }} class="h-4 w-4 text-purple-600 focus:ring-purple-500 border-gray-300 rounded">
                        <label for="is_published" class="ml-2 block text-sm text-gray-900">Publicar imediatamente</label>
                    </div>

                    <div class="flex items-center">
                        <input type="checkbox" name="is_featured" id="is_featured" value="1" {{ old('is_featured') ? 'checked' : '' }} class="h-4 w-4 text-purple-600 focus:ring-purple-500 border-gray-300 rounded">
                        <label for="is_featured" class="ml-2 block text-sm text-gray-900">Destacar evento</label>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white shadow-md rounded-lg overflow-hidden mb-6">
            <div class="bg-gray-50 px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                <h2 class="text-lg font-medium text-gray-900">Palestrantes</h2>
                <button type="button" id="add-speaker" class="inline-flex items-center px-3 py-1 border border-transparent text-sm leading-4 font-medium rounded-md text-purple-700 bg-purple-100 hover:bg-purple-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500">
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

        <div class="flex items-center justify-end">
            <a href="{{ route('admin.events.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 mr-3">
                Cancelar
            </a>

            <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-purple-600 hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500">
                Salvar Evento
            </button>
        </div>
    </form>
</div>

<!-- Template para novos palestrantes -->
<template id="speaker-template">
    <div class="speaker-card bg-gray-50 rounded-lg p-4 relative">
        <button type="button" class="remove-speaker absolute top-2 right-2 text-gray-400 hover:text-red-500">
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </button>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nome do Palestrante <span class="text-red-500">*</span></label>
                <input type="text" name="speakers[0][name]" class="speaker-name shadow-sm focus:ring-purple-500 focus:border-purple-500 block w-full sm:text-sm border-gray-300 rounded-md" required>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Instituição</label>
                    <input type="text" name="speakers[0][institution]" class="shadow-sm focus:ring-purple-500 focus:border-purple-500 block w-full sm:text-sm border-gray-300 rounded-md">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Cargo/Função</label>
                    <input type="text" name="speakers[0][role]" class="shadow-sm focus:ring-purple-500 focus:border-purple-500 block w-full sm:text-sm border-gray-300 rounded-md">
                </div>
            </div>
        </div>

        <div class="mt-4">
            <label class="block text-sm font-medium text-gray-700 mb-1">Mini Biografia</label>
            <textarea name="speakers[0][bio]" rows="2" class="shadow-sm focus:ring-purple-500 focus:border-purple-500 block w-full sm:text-sm border-gray-300 rounded-md"></textarea>
        </div>

        <div class="mt-4">
            <label class="block text-sm font-medium text-gray-700 mb-1">Foto (opcional)</label>
            <input type="file" name="speakers[0][photo]" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-purple-50 file:text-purple-700 hover:file:bg-purple-100" accept="image/*">
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
