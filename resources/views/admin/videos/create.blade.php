@extends('admin.layouts.admin-layout')

@section('page-title', 'Adicionar Novo Vídeo')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white shadow rounded-lg overflow-hidden">
        <div class="bg-stone-50 px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-900">Informações do Vídeo</h2>
        </div>

        <form action="{{ route('admin.videos.store') }}" method="POST" enctype="multipart/form-data" class="p-6 space-y-6">
            @csrf

            <div>
                <label for="title" class="block text-sm font-medium text-gray-700 mb-1">Título do Vídeo</label>
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
                <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Descrição</label>
                <textarea 
                    name="description" 
                    id="description" 
                    rows="4" 
                    class="block w-full px-4 py-2 border-1 border-gray-300 rounded text-gray-700 focus:ring-2 focus:ring-blue-500 focus:outline-transparent transition-colors"
                >{{ old('description') }}</textarea>
                @error('description')
                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="url" class="block text-sm font-medium text-gray-700 mb-1">URL do Vídeo (YouTube, Vimeo, etc.)</label>
                <input 
                    type="url" 
                    name="url" 
                    id="url" 
                    value="{{ old('url') }}" 
                    placeholder="https://www.youtube.com/watch?v=..." 
                    class="block w-full px-4 py-2 border-1 border-gray-300 rounded text-gray-700 focus:ring-2 focus:ring-blue-500 focus:outline-transparent transition-colors" 
                    required
                >
                <p class="mt-1 text-xs text-gray-500">Cole a URL completa do vídeo. URLs suportadas: YouTube, Vimeo.</p>
                @error('url')
                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="thumbnail" class="block text-sm font-medium text-gray-700 mb-1">Thumbnail (Opcional)</label>
                <input 
                    type="file" 
                    name="thumbnail" 
                    id="thumbnail" 
                    class="block w-full text-sm text-gray-700 file:mr-4 file:py-2 file:px-4 file:rounded file:border-0 file:bg-blue-500 file:text-white hover:file:bg-blue-600 transition-colors"
                >
                <p class="mt-1 text-xs text-gray-500">Formatos aceitos: JPG, PNG, GIF. Tamanho máximo: 2MB.</p>
                <p class="mt-1 text-xs text-gray-500">Se não for enviada uma thumbnail, será usada a imagem padrão do YouTube.</p>
                @error('thumbnail')
                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="playlist_id" class="block text-sm font-medium text-gray-700 mb-1">Playlist (Opcional)</label>
                <select 
                    name="playlist_id" 
                    id="playlist_id" 
                    class="block w-full px-4 py-2 border-1 border-gray-300 rounded text-gray-700 focus:ring-2 focus:ring-blue-500 focus:outline-transparent transition-colors"
                >
                    <option value="">Selecione uma playlist</option>
                    @foreach($playlists as $playlist)
                        <option value="{{ $playlist->id }}" {{ old('playlist_id') == $playlist->id ? 'selected' : '' }}>{{ $playlist->title }}</option>
                    @endforeach
                </select>
                @error('playlist_id')
                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <input 
                        type="checkbox" 
                        name="published" 
                        id="published" 
                        value="1" 
                        {{ old('published', true) ? 'checked' : '' }} 
                        class="form-checkbox h-4 w-4 text-blue-500 border-1 border-gray-300 rounded"
                    >
                    <label for="published" class="ml-2 block text-sm text-gray-700">Publicar imediatamente</label>
                </div>

                <div class="flex items-center">
                    <input 
                        type="checkbox" 
                        name="featured" 
                        id="featured" 
                        value="1" 
                        {{ old('featured') ? 'checked' : '' }} 
                        class="form-checkbox h-4 w-4 text-blue-500 border-1 border-gray-300 rounded"
                    >
                    <label for="featured" class="ml-2 block text-sm text-gray-700">Destacar vídeo</label>
                </div>
            </div>

            <div class="flex items-center justify-end gap-3">
                <a 
                    href="{{ route('admin.videos.index') }}" 
                    class="inline-flex items-center px-4 py-2 text-sm text-gray-700 bg-gray-200 rounded shadow hover:bg-gray-300 transition-colors"
                >
                    Cancelar
                </a>

                <button 
                    type="submit" 
                    class="inline-flex items-center px-4 py-2 text-sm text-white bg-blue-500 rounded shadow hover:bg-blue-600 active:bg-blue-700 transition-colors"
                >
                    Salvar Vídeo
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Preview do vídeo quando a URL é informada -->
<script>
    const urlInput = document.getElementById('url');

    // Criar o elemento de preview
    const previewContainer = document.createElement('div');
    previewContainer.className = 'mt-4 hidden';
    previewContainer.innerHTML = `
        <div class="text-sm font-medium text-gray-700 mb-2">Preview:</div>
        <div id="video-preview" class="bg-stone-50 rounded border-1 border-gray-200 shadow-xs overflow-hidden" style="max-width: 100%; aspect-ratio: 16/9;"></div>
    `;

    // Inserir após o input da URL
    urlInput.parentNode.appendChild(previewContainer);

    // Função para extrair o ID do vídeo
    function getYoutubeId(url) {
        const regExp = /^.*(youtu.be\/|v\/|u\/\w\/|embed\/|watch\?v=|&v=)([^#&?]*).*/;
        const match = url.match(regExp);
        return (match && match[2].length === 11) ? match[2] : null;
    }

    // Atualizar preview quando a URL for alterada
    urlInput.addEventListener('input', function() {
        const url = this.value.trim();
        const videoPreview = document.getElementById('video-preview');

        if (url) {
            const youtubeId = getYoutubeId(url);

            if (youtubeId) {
                // Mostrar preview
                previewContainer.classList.remove('hidden');
                videoPreview.innerHTML = `
                    <iframe width="100%" height="100%" src="https://www.youtube.com/embed/${youtubeId}" frameborder="0" allowfullscreen></iframe>
                `;
            } else {
                // Esconder preview se não for YouTube
                previewContainer.classList.add('hidden');
                videoPreview.innerHTML = '';
            }
        } else {
            // Esconder preview se a URL estiver vazia
            previewContainer.classList.add('hidden');
            videoPreview.innerHTML = '';
        }
    });

    // Verificar ao carregar a página
    window.addEventListener('DOMContentLoaded', function() {
        if (urlInput.value) {
            urlInput.dispatchEvent(new Event('input'));
        }
    });
</script>
@endsection