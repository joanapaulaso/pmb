@extends('admin.layouts.admin-layout')

@section('page-title', 'Dashboard Administrativo')

@section('content')
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
    <!-- Card - Videos -->
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center justify-between mb-4">
            <div class="flex items-center">
                <div class="p-3 bg-blue-100 rounded-full">
                    <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                    </svg>
                </div>
                <h3 class="ml-3 text-lg font-medium text-gray-900">Vídeos</h3>
            </div>
            <span class="text-2xl font-bold text-gray-900">{{ \App\Models\Video::count() }}</span>
        </div>
        <a href="{{ route('admin.videos.index') }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium flex items-center">
            Gerenciar Vídeos
            <svg class="ml-1 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
            </svg>
        </a>
    </div>

    <!-- Card - Playlists -->
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center justify-between mb-4">
            <div class="flex items-center">
                <div class="p-3 bg-indigo-100 rounded-full">
                    <svg class="h-6 w-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                    </svg>
                </div>
                <h3 class="ml-3 text-lg font-medium text-gray-900">Playlists</h3>
            </div>
            <span class="text-2xl font-bold text-gray-900">{{ \App\Models\Playlist::count() }}</span>
        </div>
        <a href="{{ route('admin.videos.index') }}#playlists" class="text-indigo-600 hover:text-indigo-800 text-sm font-medium flex items-center">
            Gerenciar Playlists
            <svg class="ml-1 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
            </svg>
        </a>
    </div>

    <!-- Card - Laboratórios -->
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center justify-between mb-4">
            <div class="flex items-center">
                <div class="p-3 bg-green-100 rounded-full">
                    <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path>
                    </svg>
                </div>
                <h3 class="ml-3 text-lg font-medium text-gray-900">Laboratórios</h3>
            </div>
            <span class="text-2xl font-bold text-gray-900">{{ \App\Models\Laboratory::count() }}</span>
        </div>
        <a href="{{ route('admin.laboratories.index') }}" class="text-green-600 hover:text-green-800 text-sm font-medium flex items-center">
            Gerenciar Laboratórios
            <svg class="ml-1 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
            </svg>
        </a>
    </div>

    <!-- Card - Eventos -->
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center justify-between mb-4">
            <div class="flex items-center">
                <div class="p-3 bg-purple-100 rounded-full">
                    <svg class="h-6 w-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                </div>
                <h3 class="ml-3 text-lg font-medium text-gray-900">Eventos</h3>
            </div>
            <span class="text-2xl font-bold text-gray-900">{{ \App\Models\Event::count() }}</span>
        </div>
        <a href="{{ route('admin.events.index') }}" class="text-purple-600 hover:text-purple-800 text-sm font-medium flex items-center">
            Gerenciar Eventos
            <svg class="ml-1 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
            </svg>
        </a>
    </div>
</div>

<!-- Atividades recentes -->
<div class="mt-8">
    <h3 class="text-lg font-medium text-gray-900 mb-4">Adições Recentes</h3>
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="divide-y divide-gray-200">
            <!-- Eventos recentes -->
            @php
                $recentEvents = \App\Models\Event::latest()->take(3)->get();
            @endphp

            @forelse($recentEvents as $event)
            <div class="p-4 flex items-center">
                <div class="flex-shrink-0 p-2 bg-purple-100 rounded-md">
                    <svg class="h-5 w-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                </div>
                <div class="ml-4 flex-1">
                    <div class="flex items-center justify-between">
                        <p class="text-sm font-medium text-gray-900">{{ $event->title }}</p>
                        <span class="text-xs text-gray-500">{{ $event->created_at->diffForHumans() }}</span>
                    </div>
                    <p class="text-xs text-gray-500">Evento adicionado</p>
                </div>
            </div>
            @empty
            <!-- No events yet -->
            @endforelse

            <!-- Vídeos recentes -->
            @php
                $recentVideos = \App\Models\Video::latest()->take(3)->get();
            @endphp

            @forelse($recentVideos as $video)
            <div class="p-4 flex items-center">
                <div class="flex-shrink-0 p-2 bg-blue-100 rounded-md">
                    <svg class="h-5 w-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                    </svg>
                </div>
                <div class="ml-4 flex-1">
                    <div class="flex items-center justify-between">
                        <p class="text-sm font-medium text-gray-900">{{ $video->title }}</p>
                        <span class="text-xs text-gray-500">{{ $video->created_at->diffForHumans() }}</span>
                    </div>
                    <p class="text-xs text-gray-500">Vídeo adicionado</p>
                </div>
            </div>
            @empty
            <!-- No videos yet -->
            @endforelse

            <!-- Laboratórios recentes -->
            @php
                $recentLabs = \App\Models\Laboratory::latest()->take(3)->get();
            @endphp

            @forelse($recentLabs as $lab)
            <div class="p-4 flex items-center">
                <div class="flex-shrink-0 p-2 bg-green-100 rounded-md">
                    <svg class="h-5 w-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path>
                    </svg>
                </div>
                <div class="ml-4 flex-1">
                    <div class="flex items-center justify-between">
                        <p class="text-sm font-medium text-gray-900">{{ $lab->name }}</p>
                        <span class="text-xs text-gray-500">{{ $lab->created_at->diffForHumans() }}</span>
                    </div>
                    <p class="text-xs text-gray-500">Laboratório adicionado</p>
                </div>
            </div>
            @empty
            <!-- No labs yet -->
            @endforelse

            @if(count($recentEvents) === 0 && count($recentVideos) === 0 && count($recentLabs) === 0)
            <div class="p-4 flex items-center justify-center">
                <p class="text-sm text-gray-500">Nenhuma atividade recente encontrada</p>
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Próximos Eventos -->
<div class="mt-8">
    <h3 class="text-lg font-medium text-gray-900 mb-4">Próximos Eventos</h3>
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="divide-y divide-gray-200">
            @php
                $upcomingEvents = \App\Models\Event::where('start_date', '>', now())
                    ->where('is_published', true)
                    ->orderBy('start_date', 'asc')
                    ->take(5)
                    ->get();
            @endphp

            @forelse($upcomingEvents as $event)
            <div class="p-4 flex items-center">
                <div class="flex-shrink-0 p-2 bg-purple-100 rounded-md">
                    <svg class="h-5 w-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                </div>
                <div class="ml-4 flex-1">
                    <div class="flex items-center justify-between">
                        <p class="text-sm font-medium text-gray-900">{{ $event->title }}</p>
                        <span class="text-xs text-gray-500">{{ $event->start_date->format('d/m/Y H:i') }}</span>
                    </div>
                    <p class="text-xs text-gray-500">{{ $event->formatted_date_range }}</p>
                </div>
            </div>
            @empty
            <div class="p-4 flex items-center justify-center">
                <p class="text-sm text-gray-500">Nenhum evento próximo encontrado</p>
            </div>
            @endforelse
        </div>
    </div>
</div>
@endsection
