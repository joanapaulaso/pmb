<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Eventos - {{ config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="antialiased bg-gray-100">
    @include('navigation-menu')

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between space-y-3 md:space-y-0 mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Eventos, Workshops e Seminários</h1>
            <div>
                <label class="text-sm font-medium text-gray-700 mr-2">Filtrar:</label>
                <select id="event-filter" class="rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 text-sm">
                    <option value="all" selected>Todos os eventos</option>
                    <option value="upcoming">Próximos eventos</option>
                    <option value="past">Eventos passados</option>
                    <option value="workshop">Workshops</option>
                    <option value="seminar">Seminários</option>
                    <option value="conference">Conferências</option>
                    <option value="webinar">Webinários</option>
                </select>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($events as $event)
                @if($event->is_published)
                    <div class="event-card bg-white rounded-lg shadow-md overflow-hidden"
                         data-type="{{ $event->event_type }}"
                         data-status="{{ $event->is_upcoming ? 'upcoming' : ($event->is_ongoing ? 'ongoing' : 'past') }}">
                        <a href="{{ route('events.show', $event->slug) }}">
                            @if($event->image_url)
                                <img src="{{ $event->image_url }}" alt="{{ $event->title }}" class="w-full h-48 object-cover">
                            @else
                                <div class="w-full h-48 bg-gray-200 flex items-center justify-center">
                                    <span class="text-gray-500">Sem Imagem</span>
                                </div>
                            @endif
                            <div class="p-4">
                                <h3 class="text-lg font-medium text-gray-900">{{ $event->title }}</h3>
                                <p class="text-sm text-gray-600 mt-1">{{ Str::limit($event->description, 100) }}</p>
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
                                <p class="text-sm text-gray-500 mt-2">{{ $event->formatted_date_range }}</p>
                                @if($event->location)
                                    <p class="text-xs text-gray-500">{{ $event->location }}</p>
                                @endif
                                @if($event->speakers->count() > 0)
                                    <p class="text-xs text-gray-500 mt-1">{{ $event->speakers->count() }} palestrante(s)</p>
                                @endif
                            </div>
                        </a>
                    </div>
                @endif
            @empty
                <p class="text-gray-600 col-span-3">Nenhum evento disponível no momento.</p>
            @endforelse
        </div>

        <div class="mt-8">
            {{ $events->links() }}
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const filterSelect = document.getElementById('event-filter');
            const eventCards = document.querySelectorAll('.event-card');

            filterSelect.addEventListener('change', function() {
                const filterValue = this.value;

                eventCards.forEach(card => {
                    const type = card.getAttribute('data-type');
                    const status = card.getAttribute('data-status');

                    if (filterValue === 'all') {
                        card.style.display = '';
                    } else if (filterValue === 'upcoming' && status === 'upcoming') {
                        card.style.display = '';
                    } else if (filterValue === 'past' && status === 'past') {
                        card.style.display = '';
                    } else if (filterValue === type) {
                        card.style.display = '';
                    } else {
                        card.style.display = 'none';
                    }
                });
            });
        });
    </script>
</body>
</html>
