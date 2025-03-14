@extends('admin.layouts.admin-layout')

@section('page-title', 'Editar Vídeo')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white shadow-md rounded-lg overflow-hidden">
        <div class="bg-gray-50 px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-medium text-gray-900">Informações do Vídeo</h2>
        </div>

        <form action="{{ route('admin.videos.update', $video) }}" method="POST" enctype="multipart/form-data" class="p-6">
            @csrf
            @method('PUT')

            <div class="mb-6">
                <label for="title" class="block text-sm font-medium text-gray-700 mb-1">Título do Vídeo</label>
                <input type="text" name="title" id="title" value="{{ old('title', $video->title) }}" class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md" required>
                @error('title')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-6">
                <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Descrição</label>
                <textarea name="description" id="description" rows="4" class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md">{{ old('description', $video->description) }}</textarea>
                @error('description')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-6">
                <label for="url" class="block text-sm font-medium text-gray-700 mb-1">URL do Vídeo (YouTube, Vimeo, etc.)</label>
                <input type="url" name="url" id="url" value="{{ old('url', $video->url) }}" placeholder="https://www.youtube.com/watch?v=..." class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md" required>
                <p class="mt-1 text-xs text-gray-500">Cole a URL completa do vídeo. URLs suportadas: YouTube, Vimeo.</p>
                @error('url')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-6">
                <label for="thumbnail" class="block text-sm font-medium text-gray-700 mb-1">Thumbnail (Opcional)</label>
                @if($video->thumbnail)
                    <div class="mb-2">
                        <img src="{{ asset('storage/' . $video->thumbnail) }}" alt="{{ $video->title }}" class="h-32 w-auto object-cover rounded">
                        <p class="mt-1 text-xs text-gray-500">Thumbnail atual</p>
                    </div>
                @endif
                <input type="file" name="thumbnail" id="thumbnail" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                <p class="mt-1 text-xs text-gray-500">Formatos aceitos: JPG, PNG, GIF. Tamanho máximo: 2MB.</p>
                <p class="mt-1 text-xs text-gray-500">Deixe em branco para manter a thumbnail atual.</p>
                @error('thumbnail')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-6">
                <label for="playlist_id" class="block text-sm font-medium text-gray-700 mb-1">Playlist (Opcional)</label>
                <select name="playlist_id" id="playlist_id" class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md">
                    <option value="">Selecione uma playlist</option>
                    @foreach($playlists as $playlist)
                        <option value="{{ $playlist->id }}" {{ old('playlist_id', $video->playlist_id) == $playlist->id ? 'selected' : '' }}>{{ $playlist->title }}</option>
                    @endforeach
                </select>
                @error('playlist_id')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex items-center justify-between mb-6">
                <div class="flex items-center">
                    <input type="checkbox" name="published" id="published" value="1" {{ old('published', $video->published) ? 'checked' : '' }} class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                    <label for="published" class="ml-2 block text-sm text-gray-900">Publicado</label>
                </div>

                <div class="flex items-center">
                    <input type="checkbox" name="featured" id="featured" value="1" {{ old('featured', $video->featured) ? 'checked' : '' }} class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                    <label for="featured" class="ml-2 block text-sm text-gray-900">Destacar vídeo</label>
                </div>
            </div>

            <div class="flex items-center justify-end">
                <a href="{{ route('admin.videos.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 mr-3">
                    Cancelar
                </a>

                <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    Atualizar Vídeo
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
    previewContainer.className = 'mt-4';
    previewContainer.innerHTML = `
        <div class="text-sm font-medium text-gray-700 mb-2">Preview:</div>
        <div id="video-preview" class="bg-gray-100 rounded-md overflow-hidden" style="max-width: 100%; aspect-ratio: 16/9;"></div>
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
