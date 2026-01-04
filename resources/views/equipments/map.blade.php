<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Mapa de Equipamentos') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                        <!-- Filtros -->
                        <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Filtros</h3>
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1" for="search-term">Buscar</label>
                                    <input id="search-term" type="text" placeholder="Título, modelo ou marca" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                                </div>
                                <div class="flex items-center space-x-2">
                                    <input id="filter-services" type="checkbox" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                    <label for="filter-services" class="text-sm text-gray-700">Prestação de serviços</label>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <input id="filter-collaboration" type="checkbox" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                    <label for="filter-collaboration" class="text-sm text-gray-700">Colaboração em projeto/convênio</label>
                                </div>
                            </div>
                        </div>

                        <!-- Lista -->
                        <div class="md:col-span-3">
                            <div class="flex items-center justify-between mb-4">
                                <h3 class="text-lg font-medium text-gray-900">Equipamentos</h3>
                                <span class="text-sm text-gray-500">Encontrados: <span id="equipments-count">0</span></span>
                            </div>
                            <div id="equipments-list" class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
                                <!-- Preenchido via JS -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        const equipments = @json($equipments ?? []);
        let filters = {
            search: '',
            services: false,
            collaboration: false,
        };

        const listEl = document.getElementById('equipments-list');
        const countEl = document.getElementById('equipments-count');
        const searchInput = document.getElementById('search-term');
        const servicesToggle = document.getElementById('filter-services');
        const collabToggle = document.getElementById('filter-collaboration');

        function applyFilters() {
            return equipments.filter(eq => {
                const term = filters.search.trim().toLowerCase();
                const matchesSearch = !term || [
                    eq.title || '',
                    eq.model || '',
                    eq.brand || '',
                ].some(field => field.toLowerCase().includes(term));

                const matchesServices = !filters.services || eq.available_for_services;
                const matchesCollab = !filters.collaboration || eq.available_for_collaboration;

                return matchesSearch && matchesServices && matchesCollab;
            });
        }

        function renderList() {
            const filtered = applyFilters();
            countEl.textContent = filtered.length;
            listEl.innerHTML = '';

            if (filtered.length === 0) {
                listEl.innerHTML = '<div class="col-span-full text-sm text-gray-500">Nenhum equipamento encontrado.</div>';
                return;
            }

            filtered.forEach(eq => {
                const card = document.createElement('div');
                card.className = 'border border-gray-200 rounded-lg p-4 bg-gray-50 shadow-sm flex flex-col space-y-2';
                card.innerHTML = `
                    <div>
                        <p class="text-xs uppercase tracking-wide text-gray-500">Laboratório</p>
                        <p class="text-sm font-semibold text-gray-900">${eq.lab_name ?? 'Não informado'}</p>
                    </div>
                    <div>
                        <p class="text-lg font-semibold text-gray-900">${eq.title ?? 'Equipamento'}</p>
                        <p class="text-sm text-gray-600">Modelo: ${eq.model ?? '—'}</p>
                        <p class="text-sm text-gray-600">Marca: ${eq.brand ?? '—'}</p>
                    </div>
                    <div>
                        <p class="text-xs font-medium text-gray-700">Disponível para:</p>
                        <ul class="text-xs text-gray-600 list-disc list-inside">
                            ${eq.available_for_services ? '<li>Prestação de serviços</li>' : ''}
                            ${eq.available_for_collaboration ? '<li>Colaboração em projeto/convênio</li>' : ''}
                            ${!eq.available_for_services && !eq.available_for_collaboration ? '<li>Não disponível para serviços/colaboração</li>' : ''}
                        </ul>
                    </div>
                    <div class="text-sm text-gray-700">
                        <p class="font-medium">Contato</p>
                        <p>${eq.contact_email ? `<a class="text-indigo-600 hover:text-indigo-800" href="mailto:${eq.contact_email}">${eq.contact_email}</a>` : 'Não informado'}</p>
                    </div>
                    ${eq.lab_id ? `<a class="text-sm text-indigo-600 hover:text-indigo-800" href="/labs/${eq.lab_id}">Ver laboratório</a>` : ''}
                `;
                listEl.appendChild(card);
            });
        }

        document.addEventListener('DOMContentLoaded', () => {
            renderList();
            searchInput?.addEventListener('input', (e) => {
                filters.search = e.target.value;
                renderList();
            });
            servicesToggle?.addEventListener('change', (e) => {
                filters.services = e.target.checked;
                renderList();
            });
            collabToggle?.addEventListener('change', (e) => {
                filters.collaboration = e.target.checked;
                renderList();
            });
        });
    </script>
</x-app-layout>
