<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $event->title }} - {{ config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="antialiased bg-gray-100">
    @include('navigation-menu')

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            @if($event->image_url)
                <img src="{{ $event->image_url }}" alt="{{ $event->title }}" class="w-full h-64 object-cover">
            @endif
            <div class="p-6">
                <h1 class="text-3xl font-bold text-gray-900">{{ $event->title }}</h1>
                <div class="mt-2">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                        {{ $event->event_type === 'workshop' ? 'bg-blue-100 text-blue-800' : '' }}
                        {{ $event->event_type === 'seminar' ? 'bg-green-100 text-green-800' : '' }}
                        {{ $event->event_type === 'conference' ? 'bg-purple-100 text-purple-800' : '' }}
                        {{ $event->event_type === 'webinar' ? 'bg-yellow-100 text-yellow-800' : '' }}
                    ">
                        {{ ucfirst($event->event_type) }}
                    </span>
                    @if($event->is_upcoming)
                        <span class="ml-1 px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                            Próximo
                        </span>
                    @elseif($event->is_ongoing)
                        <span class="ml-1 px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                            Em andamento
                        </span>
                    @else
                        <span class="ml-1 px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                            Encerrado
                        </span>
                    @endif
                    @if($event->is_featured)
                        <span class="ml-1 px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                            Destaque
                        </span>
                    @endif
                </div>
                <p class="text-gray-600 mt-4">{{ $event->description }}</p>
                <div class="mt-4">
                    <p class="text-sm text-gray-500"><strong>Data:</strong> {{ $event->formatted_date_range }}</p>
                    @if($event->location)
                        <p class="text-sm text-gray-500"><strong>Local:</strong> {{ $event->location }}</p>
                    @endif
                    @if($event->online_url)
                        <p class="text-sm text-gray-500"><strong>Link Online:</strong> <a href="{{ $event->online_url }}" class="text-indigo-600 hover:underline" target="_blank">Acessar</a></p>
                    @endif
                    @if($event->registration_url)
                        <p class="text-sm text-gray-500"><strong>Inscrição:</strong> <a href="{{ $event->registration_url }}" class="text-indigo-600 hover:underline" target="_blank">Inscrever-se</a></p>
                    @endif
                </div>

                @if($event->speakers->count() > 0)
                    <div class="mt-8">
                        <h2 class="text-xl font-semibold text-gray-800 mb-4">Palestrantes</h2>
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                            @foreach($event->speakers as $speaker)
                                <div class="flex items-start space-x-4">
                                    @if($speaker->photo)
                                        <img src="{{ asset('storage/' . $speaker->photo) }}" alt="{{ $speaker->name }}" class="h-16 w-16 rounded-full object-cover">
                                    @else
                                        <div class="h-16 w-16 rounded-full bg-gray-200 flex items-center justify-center">
                                            <span class="text-gray-500">{{ Str::upper(Str::substr($speaker->name, 0, 1)) }}</span>
                                        </div>
                                    @endif
                                    <div>
                                        <h3 class="text-lg font-medium text-gray-900">{{ $speaker->name }}</h3>
                                        @if($speaker->role)
                                            <p class="text-sm text-gray-600">{{ $speaker->role }}</p>
                                        @endif
                                        @if($speaker->institution)
                                            <p class="text-sm text-gray-600">{{ $speaker->institution }}</p>
                                        @endif
                                        @if($speaker->bio)
                                            <p class="text-sm text-gray-500 mt-1">{{ Str::limit($speaker->bio, 100) }}</p>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</body>
</html>
