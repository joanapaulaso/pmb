@extends('admin.layouts.admin-layout')

@section('page-title', 'Gerenciar Eventos')

@section('content')
<div class="flex flex-col md:flex-row md:items-center md:justify-between space-y-3 md:space-y-0 mb-6">
    <a href="{{ route('admin.events.create') }}" class="inline-flex items-center px-4 py-2 bg-purple-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-purple-700 active:bg-purple-900 focus:outline-none focus:border-purple-900 focus:ring focus:ring-purple-300 disabled:opacity-25 transition">
        <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
        </svg>
        Adicionar Evento
    </a>

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

<div class="bg-white shadow-md rounded-lg overflow-hidden">
    <div class="bg-gray-50 px-6 py-4 border-b border-gray-200">
        <h2 class="text-lg font-medium text-gray-900">Eventos, Workshops e Seminários</h2>
    </div>

    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Evento
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Tipo
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Data
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Status
                    </th>
                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Ações
                    </th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($events as $event)
                <tr class="event-row"
                    data-type="{{ $event->event_type }}"
                    data-status="{{ $event->is_upcoming ? 'upcoming' : ($event->is_ongoing ? 'ongoing' : 'past') }}">
                    <td class="px-6 py-4">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 h-12 w-20">
                                <img class="h-12 w-20 object-cover rounded" src="{{ $event->image_url }}" alt="{{ $event->title }}">
                            </div>
                            <div class="ml-4">
                                <div class="text-sm font-medium text-gray-900">{{ $event->title }}</div>
                                <div class="text-sm text-gray-500">{{ Str::limit($event->description, 60) }}</div>
                                @if ($event->speakers->count() > 0)
                                <div class="text-xs text-gray-500 mt-1">
                                    {{ $event->speakers->count() }} palestrante(s)
                                </div>
                                @endif
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                            {{ $event->event_type === 'workshop' ? 'bg-blue-100 text-blue-800' : '' }}
                            {{ $event->event_type === 'seminar' ? 'bg-green-100 text-green-800' : '' }}
                            {{ $event->event_type === 'conference' ? 'bg-purple-100 text-purple-800' : '' }}
                            {{ $event->event_type === 'webinar' ? 'bg-yellow-100 text-yellow-800' : '' }}
                        ">
                            {{ ucfirst($event->event_type) }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-900">{{ $event->formatted_date_range }}</div>
                        @if($event->location)
                        <div class="text-xs text-gray-500">{{ $event->location }}</div>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @if($event->is_upcoming)
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                Próximo
                            </span>
                        @elseif($event->is_ongoing)
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                Em andamento
                            </span>
                        @else
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                Encerrado
                            </span>
                        @endif

                        @if(!$event->is_published)
                            <span class="ml-1 px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                Rascunho
                            </span>
                        @endif

                        @if($event->is_featured)
                            <span class="ml-1 px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                Destaque
                            </span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                        <a href="{{ route('admin.events.edit', $event) }}" class="text-indigo-600 hover:text-indigo-900 mr-3">Editar</a>

                        <form action="{{ route('admin.events.destroy', $event) }}" method="POST" class="inline" onsubmit="return confirm('Tem certeza que deseja excluir este evento? Todos os palestrantes associados também serão excluídos.');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 hover:text-red-900">Excluir</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-4 text-center text-sm text-gray-500">
                        Nenhum evento encontrado.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="px-6 py-4 border-t border-gray-200">
        {{ $events->links() }}
    </div>
</div>

@push('scripts')
<script>
    // Filtro de eventos
    document.addEventListener('DOMContentLoaded', function() {
        const filterSelect = document.getElementById('event-filter');
        const eventRows = document.querySelectorAll('.event-row');

        filterSelect.addEventListener('change', function() {
            const filterValue = this.value;

            eventRows.forEach(row => {
                const type = row.getAttribute('data-type');
                const status = row.getAttribute('data-status');

                if (filterValue === 'all') {
                    row.style.display = '';
                } else if (filterValue === 'upcoming' && status === 'upcoming') {
                    row.style.display = '';
                } else if (filterValue === 'past' && status === 'past') {
                    row.style.display = '';
                } else if (filterValue === type) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });
    });
</script>
@endpush
@endsection
