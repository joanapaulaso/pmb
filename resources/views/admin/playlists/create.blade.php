@extends('admin.layouts.admin-layout')

@section('page-title', 'Adicionar Nova Playlist')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white shadow rounded-lg overflow-hidden">
        <div class="bg-stone-50 px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-900">Informações da Playlist</h2>
        </div>

        <form action="{{ route('admin.playlists.store') }}" method="POST" enctype="multipart/form-data" class="p-6 space-y-6">
            @csrf

            <div>
                <label for="title" class="block text-sm font-medium text-gray-700 mb-1">Título da Playlist</label>
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
                <label for="thumbnail" class="block text-sm font-medium text-gray-700 mb-1">Thumbnail (Opcional)</label>
                <input 
                    type="file" 
                    name="thumbnail" 
                    id="thumbnail" 
                    class="block w-full text-sm text-gray-700 file:mr-4 file:py-2 file:px-4 file:rounded file:border-0 file:bg-blue-500 file:text-white hover:file:bg-blue-600 transition-colors"
                >
                <p class="mt-1 text-xs text-gray-500">Formatos aceitos: JPG, PNG, GIF. Tamanho máximo: 2MB.</p>
                <p class="mt-1 text-xs text-gray-500">Se não for enviada uma thumbnail, será usada a imagem do primeiro vídeo da playlist.</p>
                @error('thumbnail')
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
                    <label for="featured" class="ml-2 block text-sm text-gray-700">Destacar playlist</label>
                </div>
            </div>

            <div class="flex items-center justify-end gap-3">
                <a 
                    href="{{ route('admin.videos.index') }}#playlists" 
                    class="inline-flex items-center px-4 py-2 text-sm text-gray-700 bg-gray-200 rounded shadow hover:bg-gray-300 transition-colors"
                >
                    Cancelar
                </a>

                <button 
                    type="submit" 
                    class="inline-flex items-center px-4 py-2 text-sm text-white bg-blue-500 rounded shadow hover:bg-blue-600 active:bg-blue-700 transition-colors"
                >
                    Salvar Playlist
                </button>
            </div>
        </form>
    </div>
</div>
@endsection