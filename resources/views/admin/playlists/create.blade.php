
@extends('admin.layouts.admin-layout')

@section('page-title', 'Adicionar Nova Playlist')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white shadow-md rounded-lg overflow-hidden">
        <div class="bg-gray-50 px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-medium text-gray-900">Informações da Playlist</h2>
        </div>

        <form action="{{ route('admin.playlists.store') }}" method="POST" enctype="multipart/form-data" class="p-6">
            @csrf

            <div class="mb-6">
                <label for="title" class="block text-sm font-medium text-gray-700 mb-1">Título da Playlist</label>
                <input type="text" name="title" id="title" value="{{ old('title') }}" class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md" required>
                @error('title')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-6">
                <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Descrição</label>
                <textarea name="description" id="description" rows="4" class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md">{{ old('description') }}</textarea>
                @error('description')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-6">
                <label for="thumbnail" class="block text-sm font-medium text-gray-700 mb-1">Thumbnail (Opcional)</label>
                <input type="file" name="thumbnail" id="thumbnail" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                <p class="mt-1 text-xs text-gray-500">Formatos aceitos: JPG, PNG, GIF. Tamanho máximo: 2MB.</p>
                <p class="mt-1 text-xs text-gray-500">Se não for enviada uma thumbnail, será usada a imagem do primeiro vídeo da playlist.</p>
                @error('thumbnail')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex items-center justify-between mb-6">
                <div class="flex items-center">
                    <input type="checkbox" name="published" id="published" value="1" {{ old('published', true) ? 'checked' : '' }} class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                    <label for="published" class="ml-2 block text-sm text-gray-900">Publicar imediatamente</label>
                </div>

                <div class="flex items-center">
                    <input type="checkbox" name="featured" id="featured" value="1" {{ old('featured') ? 'checked' : '' }} class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                    <label for="featured" class="ml-2 block text-sm text-gray-900">Destacar playlist</label>
                </div>
            </div>

            <div class="flex items-center justify-end">
                <a href="{{ route('admin.videos.index') }}#playlists" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 mr-3">
                    Cancelar
                </a>

                <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    Salvar Playlist
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
