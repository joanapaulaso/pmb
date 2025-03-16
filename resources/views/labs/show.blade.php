<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ $labData['name'] }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6">
                    <!-- Card com informações principais -->
                    <div class="bg-gray-50 p-4 rounded-lg border border-gray-200 mb-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-2">{{ $labData['name'] }}</h3>
                        <p class="text-sm text-gray-600 mb-4">{{ $labData['formatted_address'] }}</p>

                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            <div>
                                <h4 class="text-sm font-semibold mb-1">Localização</h4>
                                <p class="text-sm">
                                    @if ($labData['details']['building']) Prédio: {{ $labData['details']['building'] }}<br> @endif
                                    @if ($labData['details']['floor']) Andar: {{ $labData['details']['floor'] }}<br> @endif
                                    @if ($labData['details']['room']) Sala: {{ $labData['details']['room'] }}<br> @endif
                                    @if ($labData['details']['department']) Departamento: {{ $labData['details']['department'] }}<br> @endif
                                    @if ($labData['details']['campus']) Campus: {{ $labData['details']['campus'] }} @endif
                                </p>
                            </div>

                            <div>
                                <h4 class="text-sm font-semibold mb-1">Contato</h4>
                                <p class="text-sm">
                                    @if ($labData['details']['phone']) Telefone: {{ $labData['details']['phone'] }}<br> @endif
                                    @if ($labData['details']['contact_email']) Email: {{ $labData['details']['contact_email'] }} @endif
                                </p>
                            </div>

                            <div>
                                <h4 class="text-sm font-semibold mb-1">Horário</h4>
                                <p class="text-sm">{{ $labData['details']['working_hours'] ?? 'Não informado' }}</p>
                            </div>
                        </div>

                        <div class="mt-4 flex space-x-4">
                            @if ($labData['details']['website'])
                                <a href="{{ $labData['details']['website'] }}" target="_blank" class="text-indigo-600 hover:text-indigo-800 text-sm">Visitar website</a>
                            @endif
                            @if ($labData['coordinates'])
                                <a href="https://www.google.com/maps/dir/?api=1&destination={{ $labData['coordinates']['lat'] }},{{ $labData['coordinates']['lng'] }}" target="_blank" class="text-indigo-600 hover:text-indigo-800 text-sm">Como chegar</a>
                            @endif
                        </div>
                    </div>

                    <!-- Seção de Equipamentos -->
                    @if (!empty($labData['equipments']))
                        <div class="mt-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Equipamentos do Laboratório</h3>
                            <div class="relative">
                                <div class="swiper-container equipment-carousel">
                                    <div class="swiper-wrapper">
                                        @foreach ($labData['equipments'] as $equipment)
                                            <div class="swiper-slide">
                                                <div class="bg-gray-50 p-3 rounded-lg border border-gray-200 h-full flex flex-col">
                                                    @if ($equipment['photo_path'])
                                                        <div class="mb-3">
                                                            <img
                                                                src="{{ $equipment['photo_path'] }}"
                                                                alt="{{ $equipment['model'] }}"
                                                                class="w-full h-32 object-cover rounded cursor-pointer"
                                                                onclick="openModal('{{ $equipment['photo_path'] }}', '{{ $equipment['model'] }}')"
                                                            >
                                                        </div>
                                                    @endif
                                                    <h4 class="text-sm font-semibold truncate">{{ $equipment['model'] }}</h4>
                                                    <p class="text-xs text-gray-600 truncate">Marca: {{ $equipment['brand'] }}</p>
                                                    <p class="text-xs text-gray-600 truncate">Responsável: {{ $equipment['technical_responsible'] }}</p>
                                                    <div class="mt-1">
                                                        <p class="text-xs font-medium">Disponível para:</p>
                                                        <ul class="text-xs text-gray-600">
                                                            @if ($equipment['available_for_services'])
                                                                <li>- Prestação de serviços</li>
                                                            @endif
                                                            @if ($equipment['available_for_collaboration'])
                                                                <li>- Colaboração em projeto/convênio</li>
                                                            @endif
                                                            @if (!$equipment['available_for_services'] && !$equipment['available_for_collaboration'])
                                                                <li>- Não disponível</li>
                                                            @endif
                                                        </ul>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                                <!-- Navegação personalizada -->
                                <div class="absolute top-1/2 -translate-y-1/2 left-0 z-10">
                                    <button class="equipment-swiper-button-prev text-gray-500 hover:text-gray-700">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                                        </svg>
                                    </button>
                                </div>
                                <div class="absolute top-1/2 -translate-y-1/2 right-0 z-10">
                                    <button class="equipment-swiper-button-next text-gray-500 hover:text-gray-700">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Seção de Posts -->
                    <div class="mt-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Postagens do Laboratório</h3>

                        <!-- Filtro de Tags -->
                        <div class="flex flex-wrap gap-2 mb-4">
                            @foreach($tags as $tag)
                                <button type="button"
                                        class="tag-button inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold {{ in_array($tag, $selectedTags) ? 'bg-gray-800 text-white' : $tagColors[$tag] }}"
                                        data-tag="{{ $tag }}"
                                        data-original-styles="{{ $tagColors[$tag] }}">
                                    #{{ $tag }}
                                </button>
                            @endforeach
                        </div>

                        <!-- Filtro para publicações do laboratório -->
                        <div class="mb-4">
                            <label class="flex items-center space-x-3 cursor-pointer">
                                <input type="checkbox"
                                       id="lab-filter-toggle"
                                       class="hidden peer"
                                       data-active="{{ request()->query('lab_filter', 'false') === 'true' ? 'true' : 'false' }}"
                                       @if (request()->query('lab_filter', 'false') === 'true') checked @endif>
                                <div class="relative w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-indigo-300 dark:peer-focus:ring-indigo-800 rounded-full peer dark:bg-gray-300 peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-300 peer-checked:bg-indigo-600 dark:peer-checked:bg-indigo-600"></div>
                                <span class="text-sm font-medium text-gray-600 peer-checked:text-indigo-600 transition-colors duration-300">
                                    Ver apenas publicações de membros
                                </span>
                            </label>
                        </div>

                        <input type="hidden" id="selected-tags" value="{{ implode(',', $selectedTags) }}">
                        <input type="hidden" id="lab-filter" value="{{ request()->query('lab_filter', 'false') }}">

                        <!-- Lista de Posts -->
                        <div id="posts-container">
                            @include('components.labs-post-list', ['posts' => $posts, 'tags' => $tags, 'selectedTags' => $selectedTags])
                        </div>

                        <!-- Paginação -->
                        <div class="mt-6">
                            {{ $posts->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para exibir a foto -->
    <div id="photo-modal" class="fixed inset-0 bg-black bg-opacity-75 hidden flex items-center justify-center z-50" onclick="closeModalOnOutsideClick(event)">
        <div class="relative max-w-4xl w-full" onclick="event.stopPropagation()">
            <button class="absolute top-4 right-4 text-white text-2xl" onclick="closeModal()">×</button>
            <img id="modal-image" src="" alt="" class="w-full h-auto rounded">
            <p id="modal-caption" class="text-white text-center mt-2"></p>
        </div>
    </div>

    <script>
        // Configurações para filtro de tags
        window.csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        window.tagColors = @json($tagColors);

        document.addEventListener('DOMContentLoaded', () => {
            // Inicializar o Swiper
            const swiper = new Swiper('.equipment-carousel', {
                slidesPerView: 1,
                spaceBetween: 10,
                navigation: {
                    nextEl: '.equipment-swiper-button-next',
                    prevEl: '.equipment-swiper-button-prev',
                },
                breakpoints: {
                    640: {
                        slidesPerView: 2,
                        spaceBetween: 15,
                    },
                    1024: {
                        slidesPerView: 3,
                        spaceBetween: 20,
                    },
                },
            });

            // Configurar filtros de tags
            document.querySelectorAll('.tag-button').forEach(button => {
                const tag = button.getAttribute('data-tag');
                button.addEventListener('click', function() {
                    window.toggleTag(this, tag);
                });
            });

            const labFilterToggle = document.getElementById('lab-filter-toggle');
            const labFilterInput = document.getElementById('lab-filter');
            const isLabFilterActive = labFilterInput.value === 'true';
            updateLabFilterButtonStyle(labFilterToggle, isLabFilterActive);

            labFilterToggle.addEventListener('change', function() {
                const isActive = this.checked;
                labFilterInput.value = isActive.toString();
                updateLabFilterButtonStyle(this, isActive);

                const selectedTagsInput = document.getElementById('selected-tags');
                const selectedTags = selectedTagsInput.value ? selectedTagsInput.value.split(',') : [];
                const queryParams = new URLSearchParams({
                    tags: selectedTags.join(','),
                    lab_filter: isActive.toString()
                }).toString();
                window.location.href = `/labs/{{ $labData['id'] }}?${queryParams}`;
            });
        });

        window.toggleTag = function(element, tag) {
            const selectedTagsInput = document.getElementById('selected-tags');
            let selectedTags = selectedTagsInput.value ? selectedTagsInput.value.split(',') : [];

            if (tag === 'all') {
                if (selectedTags.includes('all')) {
                    selectedTags = [];
                    document.querySelectorAll('.tag-button').forEach(btn => {
                        btn.classList.remove('bg-gray-800', 'text-white');
                        const originalStyles = btn.getAttribute('data-original-styles');
                        if (originalStyles) originalStyles.split(' ').forEach(cls => btn.classList.add(cls));
                    });
                } else {
                    selectedTags = ['all'];
                    document.querySelectorAll('.tag-button').forEach(btn => {
                        const btnTag = btn.getAttribute('data-tag');
                        const originalStyles = btn.getAttribute('data-original-styles');
                        if (originalStyles) originalStyles.split(' ').forEach(cls => btn.classList.remove(cls));
                        btn.classList.toggle('bg-gray-800', btnTag === 'all');
                        btn.classList.toggle('text-white', btnTag === 'all');
                        if (btnTag !== 'all' && originalStyles) originalStyles.split(' ').forEach(cls => btn.classList.add(cls));
                    });
                }
            } else {
                if (selectedTags.includes('all')) {
                    selectedTags = selectedTags.filter(t => t !== 'all');
                    const allBtn = document.querySelector('.tag-button[data-tag="all"]');
                    if (allBtn) {
                        allBtn.classList.remove('bg-gray-800', 'text-white');
                        const originalStyles = allBtn.getAttribute('data-original-styles');
                        if (originalStyles) originalStyles.split(' ').forEach(cls => allBtn.classList.add(cls));
                    }
                }

                if (selectedTags.includes(tag)) {
                    selectedTags = selectedTags.filter(t => t !== tag);
                    element.classList.remove('bg-gray-800', 'text-white');
                    const originalStyles = element.getAttribute('data-original-styles');
                    if (originalStyles) originalStyles.split(' ').forEach(cls => element.classList.add(cls));
                } else if (selectedTags.length < 3) {
                    selectedTags.push(tag);
                    const originalStyles = element.getAttribute('data-original-styles');
                    if (originalStyles) originalStyles.split(' ').forEach(cls => element.classList.remove(cls));
                    element.classList.add('bg-gray-800', 'text-white');
                }
            }

            selectedTagsInput.value = selectedTags.join(',');
            const labFilter = document.getElementById('lab-filter').value;
            const queryParams = new URLSearchParams({
                tags: selectedTags.join(','),
                lab_filter: labFilter
            }).toString();
            window.location.href = `/labs/{{ $labData['id'] }}?${queryParams}`;
        };

        function updateLabFilterButtonStyle(element, isActive) {
            const parentLabel = element.parentElement;
            const textSpan = parentLabel.querySelector('span.text-sm');
            if (isActive) {
                textSpan.classList.add('text-indigo-600');
            } else {
                textSpan.classList.remove('text-indigo-600');
            }
        }

        // Funções do Modal
        function openModal(imageSrc, caption) {
            const modal = document.getElementById('photo-modal');
            const modalImage = document.getElementById('modal-image');
            const modalCaption = document.getElementById('modal-caption');
            modalImage.src = imageSrc;
            modalCaption.textContent = caption;
            modal.classList.remove('hidden');
        }

        function closeModal() {
            const modal = document.getElementById('photo-modal');
            modal.classList.add('hidden');
        }

        function closeModalOnOutsideClick(event) {
            const modal = document.getElementById('photo-modal');
            const modalContent = modal.querySelector('.relative');
            if (!modalContent.contains(event.target)) {
                closeModal();
            }
        }
    </script>

    <style>
        /* Ajustes para o carrossel de equipamentos */
        .equipment-carousel {
            padding: 0;
        }

        .equipment-carousel .swiper-slide {
            width: 250px; /* Tamanho fixo para os cards */
            height: auto;
        }

        .equipment-carousel img {
            max-height: 128px; /* Ajuste a altura da imagem para ser mais compacta */
            object-fit: cover;
        }

        /* Estilizar as setas de navegação */
        .equipment-swiper-button-prev,
        .equipment-swiper-button-next {
            width: 24px;
            height: 24px;
            background: rgba(255, 255, 255, 0.8);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }

        .equipment-swiper-button-prev svg,
        .equipment-swiper-button-next svg {
            width: 16px;
            height: 16px;
        }

        /* Esconder a paginação padrão do Swiper */
        .equipment-carousel .swiper-pagination {
            display: none;
        }

        /* Ajustar o texto para evitar overflow */
        .equipment-carousel .truncate {
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
    </style>
</x-app-layout>
