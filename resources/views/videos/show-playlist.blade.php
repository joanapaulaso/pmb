<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $playlist->title }} - {{ config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="antialiased bg-gray-100">
    @include('navigation-menu')

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">{{ $playlist->title }}</h1>
            <p class="text-gray-600 mt-2">{{ $playlist->description }}</p>
        </div>

        @if($playlist->videos->count() > 0)
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($playlist->videos as $video)
                    <div class="bg-white rounded-lg shadow-md overflow-hidden">
                        <a href="{{ $video->url }}" target="_blank">
                            @if($video->thumbnail)
                                <img src="{{ asset('storage/' . $video->thumbnail) }}" alt="{{ $video->title }}" class="w-full h-48 object-cover">
                            @else
                                <div class="w-full h-48 bg-gray-200 flex items-center justify-center">
                                    <span class="text-gray-500">Sem Thumbnail</span>
                                </div>
                            @endif
                            <div class="p-4">
                                <h3 class="text-lg font-medium text-gray-900">{{ $video->title }}</h3>
                                <p class="text-sm text-gray-600 mt-1">{{ Str::limit($video->description, 100) }}</p>
                            </div>
                        </a>
                    </div>
                @endforeach
            </div>
        @else
            <p class="text-gray-600">Nenhum vídeo nesta playlist.</p>
        @endif
    </div>
</body>
</html>
