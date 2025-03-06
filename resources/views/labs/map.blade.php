<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Mapa de Laboratórios') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                        <!-- Painel de Filtros e Lista -->
                        <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Laboratórios</h3>

                            <!-- Filtros -->
                            <div class="mb-4">
                                <label for="filter-search" class="block text-sm font-medium text-gray-700 mb-1">Pesquisar</label>
                                <input type="text" id="filter-search" placeholder="Nome ou endereço..." class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                            </div>

                            <div class="mb-4">
                                <label for="filter-department" class="block text-sm font-medium text-gray-700 mb-1">Departamento</label>
                                <select id="filter-department" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                                    <option value="">Todos os departamentos</option>
                                </select>
                            </div>

                            <div class="mb-4">
                                <label for="filter-campus" class="block text-sm font-medium text-gray-700 mb-1">Campus</label>
                                <select id="filter-campus" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                                    <option value="">Todos os campi</option>
                                </select>
                            </div>

                            <div class="mb-4">
                                <label class="inline-flex items-center">
                                    <input type="checkbox" id="filter-accessibility" class="form-checkbox h-5 w-5 text-indigo-600">
                                    <span class="ml-2 text-sm text-gray-700">Acessibilidade</span>
                                </label>
                            </div>

                            <!-- Lista de Laboratórios -->
                            <div class="mt-6">
                                <h4 class="text-sm font-semibold mb-2">Laboratórios encontrados: <span id="labs-count">0</span></h4>
                                <div id="labs-list" class="mt-2 divide-y divide-gray-200 max-h-[500px] overflow-y-auto">
                                    <!-- Lista será preenchida com JavaScript -->
                                </div>
                            </div>
                        </div>

                        <!-- Mapa Principal -->
                        <div class="md:col-span-3 bg-gray-100 rounded-lg border border-gray-200 h-[700px]">
                            <div id="map-container" class="w-full h-full">
                                <gmp-map id="labs-map"
                                     center="-16.3141633,-52.6125466"
                                     zoom="4"
                                     map-id="75d0118fd3ecfce6"
                                     class="w-full h-full">
                                </gmp-map>
                            </div>
                        </div>
                    </div>

                    <!-- Detalhes do laboratório selecionado -->
                    <div id="lab-details" class="mt-6 bg-gray-50 p-4 rounded-lg border border-gray-200 hidden">
                        <h3 id="lab-detail-name" class="text-lg font-medium text-gray-900 mb-2"></h3>
                        <p id="lab-detail-address" class="text-sm text-gray-600 mb-4"></p>
                        <!-- Detalhes do laboratório... -->
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- JavaScript para o Mapa -->
    <script>
        // Variáveis globais
        let map;
        let markers = [];
        let infoWindow;
        let labs = @json($formattedLabs ?? []);

        // Debug - verificar dados recebidos
        console.log('Dados de laboratórios recebidos:', labs);

        // Filtros
        let departments = new Set();
        let campuses = new Set();
        let currentFilters = {
            search: '',
            department: '',
            campus: '',
            accessibility: false
        };

        // Inicialização do mapa
        async function initMap() {
            try {
                // Aguardar pela definição do componente do mapa
                await customElements.whenDefined('gmp-map');
                console.log('Componente gmp-map definido');

                map = document.getElementById('labs-map');

                if (!map) {
                    console.error('Elemento do mapa não encontrado');
                    return;
                }

                console.log('Mapa encontrado:', map);

                // Aguardar até que o mapa esteja realmente inicializado
                await new Promise(resolve => {
                    if (map.innerMap) {
                        resolve();
                    } else {
                        const checkMap = setInterval(() => {
                            if (map.innerMap) {
                                clearInterval(checkMap);
                                resolve();
                            }
                        }, 100);
                    }
                });

                console.log('Mapa interno inicializado');

                // Configurar mapa com mais controles
                map.innerMap.setOptions({
                    mapTypeControl: true,
                    streetViewControl: true,
                    fullscreenControl: true,
                    zoomControl: true,
                    styles: [
                        {
                            featureType: "poi",
                            elementType: "labels",
                            stylers: [{ visibility: "on" }]
                        }
                    ]
                });

                // Criar infoWindow com estilo personalizado
                infoWindow = new google.maps.InfoWindow({
                    maxWidth: 300,
                    pixelOffset: new google.maps.Size(0, -30)
                });

                // Verificar se temos laboratórios com coordenadas
                if (labs.length === 0) {
                    console.warn('Nenhum laboratório com coordenadas encontrado');
                    document.getElementById('labs-count').textContent = '0';
                    const listContainer = document.getElementById('labs-list');
                    listContainer.innerHTML = '<div class="py-2 text-sm text-gray-500">Nenhum laboratório com coordenadas cadastradas encontrado</div>';
                    return;
                }

                // Inicializar filtros
                initFilters();

                // Inicializar marcadores - isso vai mostrar todos os laboratórios no mapa
                createMarkers();

                // Inicializar lista de laboratórios
                updateLabsList();

                // Atualizar contagem
                document.getElementById('labs-count').textContent = labs.length;

                console.log('Inicialização do mapa concluída com sucesso');
            } catch (error) {
                console.error('Erro ao inicializar o mapa:', error);
            }
        }

        // Criar marcadores para cada laboratório
        function createMarkers() {
            try {
                console.log('Criando marcadores para', labs.length, 'laboratórios');

                // Limpar marcadores existentes
                clearMarkers();

                // Aplicar filtros
                const filteredLabs = applyFilters();
                console.log('Laboratórios filtrados:', filteredLabs.length);

                // Criar novos marcadores
                const bounds = new google.maps.LatLngBounds();
                let markersCreated = 0;

                filteredLabs.forEach(lab => {
                    console.log('Criando marcador para:', lab.name, 'nas coordenadas:', lab.coordinates);

                    try {
                        // Usar o marcador padrão em vez do AdvancedMarkerElement
                        const marker = new google.maps.Marker({
                            position: lab.coordinates,
                            map: map.innerMap,
                            title: lab.name,
                            // Ícone personalizado opcional
                            icon: {
                                path: google.maps.SymbolPath.MARKER,
                                fillColor: '#4F46E5',
                                fillOpacity: 1,
                                strokeWeight: 1,
                                scale: 10,
                                labelOrigin: new google.maps.Point(0, -3)
                            },
                            label: {
                                text: lab.id.toString(),
                                color: 'white',
                                fontSize: '10px'
                            }
                        });

                        // Atribuir dados do laboratório ao marcador
                        marker.lab = lab;

                        // Adicionar evento de clique
                        marker.addListener('click', () => {
                            showLabDetails(lab);
                            infoWindow.setContent(
                                `<div class="p-3 min-w-[200px]">
                                    <h3 class="font-bold text-base">${lab.name}</h3>
                                    <p class="text-sm mt-1">${lab.address || 'Endereço não informado'}</p>
                                    <div class="mt-2">
                                        <a href="/teams/${lab.id}" class="text-blue-600 hover:text-blue-800 text-xs">Ver detalhes</a>
                                    </div>
                                </div>`
                            );
                            infoWindow.open(map.innerMap, marker);
                        });

                        // Adicionar ao array de marcadores
                        markers.push(marker);
                        markersCreated++;

                        // Expandir os limites do mapa
                        bounds.extend(lab.coordinates);

                        console.log('Marcador criado com sucesso');
                    } catch (markerError) {
                        console.error('Erro ao criar marcador:', markerError, 'para laboratório:', lab);
                    }
                });

                console.log(`${markersCreated} marcadores criados com sucesso`);

                // Ajustar visualização do mapa
                if (markers.length > 0) {
                    console.log('Ajustando limites do mapa para', markers.length, 'marcadores');

                    // Pequeno atraso para garantir que o mapa esteja totalmente carregado
                    setTimeout(() => {
                        map.innerMap.fitBounds(bounds);

                        // Limitar o zoom máximo para não aproximar demais quando há apenas um marcador
                        if (markers.length === 1) {
                            map.innerMap.setZoom(Math.min(15, map.innerMap.getZoom()));
                        }
                    }, 300);
                } else {
                    console.warn('Nenhum marcador criado após filtros');
                }

                // Atualizar contagem
                document.getElementById('labs-count').textContent = filteredLabs.length;
            } catch (error) {
                console.error('Erro ao criar marcadores:', error);
            }
        }

        // Função para criar o conteúdo visual do marcador
        function buildMarkerContent(lab) {
            // Criar um elemento para o marcador personalizado
            const element = document.createElement('div');

            // Definir a aparência do pin - mais chamativo e visível
            element.innerHTML = `
                <div class="marker-container" style="cursor: pointer; width: 30px; height: 40px; position: relative;">
                    <svg viewBox="0 0 24 24" width="30" height="40" fill="#4F46E5">
                        <path d="M12 0C7.31 0 3.5 3.81 3.5 8.5c0 5.25 8.5 15.5 8.5 15.5s8.5-10.25 8.5-15.5C20.5 3.81 16.69 0 12 0zm0 13a4.5 4.5 0 110-9 4.5 4.5 0 010 9z"/>
                    </svg>
                    <div class="marker-label" style="position: absolute; top: 7px; left: 0; width: 100%; color: white; font-size: 10px; font-weight: bold; text-align: center;">
                        ${lab.id}
                    </div>
                </div>
            `;

            return element;
        }

        // Limpar marcadores existentes
        function clearMarkers() {
            markers.forEach(marker => {
                marker.setMap(null);
            });
            markers = [];
            console.log('Marcadores removidos');
        }

        // Aplicar filtros aos laboratórios
        function applyFilters() {
            return labs.filter(lab => {
                // Filtro de pesquisa
                if (currentFilters.search) {
                    const searchTerm = currentFilters.search.toLowerCase();
                    const nameMatch = lab.name.toLowerCase().includes(searchTerm);
                    const addressMatch = lab.address && lab.address.toLowerCase().includes(searchTerm);
                    if (!nameMatch && !addressMatch) return false;
                }

                // Filtro de departamento
                if (currentFilters.department && lab.details.department !== currentFilters.department) {
                    return false;
                }

                // Filtro de campus
                if (currentFilters.campus && lab.details.campus !== currentFilters.campus) {
                    return false;
                }

                // Filtro de acessibilidade
                if (currentFilters.accessibility && !lab.details.has_accessibility) {
                    return false;
                }

                return true;
            });
        }

        // Inicializar filtros
        function initFilters() {
            // Coletar valores únicos para os filtros
            labs.forEach(lab => {
                if (lab.details.department) departments.add(lab.details.department);
                if (lab.details.campus) campuses.add(lab.details.campus);
            });

            // Preencher os selects de filtro
            const departmentSelect = document.getElementById('filter-department');
            const campusSelect = document.getElementById('filter-campus');

            // Departamentos
            departments.forEach(dept => {
                const option = document.createElement('option');
                option.value = dept;
                option.textContent = dept;
                departmentSelect.appendChild(option);
            });

            // Campi
            campuses.forEach(campus => {
                const option = document.createElement('option');
                option.value = campus;
                option.textContent = campus;
                campusSelect.appendChild(option);
            });

            // Adicionar event listeners
            document.getElementById('filter-search').addEventListener('input', function(e) {
                currentFilters.search = e.target.value;
                createMarkers();
                updateLabsList();
            });

            departmentSelect.addEventListener('change', function(e) {
                currentFilters.department = e.target.value;
                createMarkers();
                updateLabsList();
            });

            campusSelect.addEventListener('change', function(e) {
                currentFilters.campus = e.target.value;
                createMarkers();
                updateLabsList();
            });

            document.getElementById('filter-accessibility').addEventListener('change', function(e) {
                currentFilters.accessibility = e.target.checked;
                createMarkers();
                updateLabsList();
            });

            console.log('Filtros inicializados');
        }

        // Atualizar a lista de laboratórios
        function updateLabsList() {
            try {
                const listContainer = document.getElementById('labs-list');
                listContainer.innerHTML = '';

                const filteredLabs = applyFilters();
                console.log('Atualizando lista com', filteredLabs.length, 'laboratórios');

                if (filteredLabs.length === 0) {
                    listContainer.innerHTML = '<div class="py-2 text-sm text-gray-500">Nenhum laboratório encontrado</div>';
                    return;
                }

                filteredLabs.forEach(lab => {
                    const item = document.createElement('div');
                    item.className = 'py-3 cursor-pointer hover:bg-gray-100';
                    item.innerHTML = `
                        <h5 class="text-sm font-medium text-gray-900">${lab.name}</h5>
                        <p class="text-xs text-gray-500 mt-1">${lab.address || 'Endereço não informado'}</p>
                        ${lab.details.department ? `<p class="text-xs text-gray-400 mt-1">${lab.details.department}</p>` : ''}
                    `;

                    // Adicionar evento de clique
                    item.addEventListener('click', () => {
                        // Centralizar no mapa
                        map.innerMap.setCenter(lab.coordinates);
                        map.innerMap.setZoom(17);

                        // Mostrar infoWindow do marcador correspondente
                        const marker = markers.find(m => m.lab.id === lab.id);
                        if (marker) {
                            google.maps.event.trigger(marker, 'click');
                        }

                        // Mostrar detalhes
                        showLabDetails(lab);
                    });

                    listContainer.appendChild(item);
                });
            } catch (error) {
                console.error('Erro ao atualizar lista de laboratórios:', error);
            }
        }

        // Mostrar detalhes do laboratório selecionado
        function showLabDetails(lab) {
            try {
                const detailContainer = document.getElementById('lab-details');

                // Preencher os detalhes
                document.getElementById('lab-detail-name').textContent = lab.name;
                document.getElementById('lab-detail-address').textContent = lab.address || 'Endereço não informado';

                // Localização
                let locationText = '';
                if (lab.details.building) locationText += `Prédio: ${lab.details.building}<br>`;
                if (lab.details.floor) locationText += `Andar: ${lab.details.floor}<br>`;
                if (lab.details.room) locationText += `Sala: ${lab.details.room}<br>`;
                if (lab.details.department) locationText += `Departamento: ${lab.details.department}<br>`;
                if (lab.details.campus) locationText += `Campus: ${lab.details.campus}`;
                document.getElementById('lab-detail-location').innerHTML = locationText || 'Informações não disponíveis';

                // Contato
                let contactText = '';
                if (lab.details.phone) contactText += `Telefone: ${lab.details.phone}<br>`;
                if (lab.details.contact_email) contactText += `Email: ${lab.details.contact_email}`;
                document.getElementById('lab-detail-contact').innerHTML = contactText || 'Informações não disponíveis';

                // Horário
                document.getElementById('lab-detail-hours').textContent = lab.details.working_hours || 'Não informado';

                // Links
                const websiteLink = document.getElementById('lab-detail-website');
                if (lab.details.website) {
                    websiteLink.href = lab.details.website;
                    websiteLink.classList.remove('hidden');
                } else {
                    websiteLink.classList.add('hidden');
                }

                // Link para direções
                const directionsLink = document.getElementById('lab-detail-directions');
                if (lab.coordinates) {
                    const directionsUrl = `https://www.google.com/maps/dir/?api=1&destination=${lab.coordinates.lat},${lab.coordinates.lng}`;
                    directionsLink.href = directionsUrl;
                    directionsLink.classList.remove('hidden');
                } else {
                    directionsLink.classList.add('hidden');
                }

                // Link para perfil completo
                document.getElementById('lab-detail-view').href = `/teams/${lab.id}`;

                // Mostrar o container
                detailContainer.classList.remove('hidden');

                console.log('Detalhes do laboratório exibidos:', lab.name);
            } catch (error) {
                console.error('Erro ao mostrar detalhes do laboratório:', error);
            }
        }

        // Inicializar quando o DOM estiver pronto
        document.addEventListener('DOMContentLoaded', () => {
            console.log('Inicializando mapa de laboratórios');
            initMap();
        });

        document.addEventListener('DOMContentLoaded', function() {
            const reloadButton = document.getElementById('reload-map-button');
            if (reloadButton) {
                reloadButton.addEventListener('click', function() {
                    console.log('Recarregando mapa...');
                    // Limpar e recriar os marcadores
                    clearMarkers();
                    createMarkers();

                    // Exibir mensagem de sucesso
                    alert('Mapa recarregado com sucesso!');
                });
            }
        });
    </script>
</x-app-layout>
