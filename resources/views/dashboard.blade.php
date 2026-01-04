<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    @php
        $tagColors = config('tags.colors', []);
        $shouldOpenComposer = $errors->any() || old('content');
    @endphp

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="flex flex-col md:flex-row md:space-x-6">
                <!-- Coluna esquerda (menu + tags) -->
                <aside class="md:w-1/4 mb-6 md:mb-0 space-y-4 md:sticky md:top-5 self-start">
                    <div class="bg-white shadow-sm rounded-lg border border-gray-100 p-4">
                        <h3 class="text-lg font-semibold text-gray-800 mb-3">Publicar</h3>
                        <p class="text-sm text-gray-600 mb-3">Compartilhe algo com a comunidade.</p>
                        <button type="button" data-open-composer class="inline-flex items-center justify-center w-full px-4 py-2 rounded-full bg-indigo-600 text-white font-semibold text-sm hover:bg-indigo-700 transition">
                            Escrever post
                        </button>
                    </div>

                    <div id="tags-section" class="bg-white shadow-sm rounded-lg border border-gray-100 p-4 mt-4">
                        <div class="flex items-center justify-between mb-3">
                            <h3 class="text-lg font-semibold text-gray-800">Tags</h3>
                            @if(!empty($selectedTags))
                                <a href="{{ route('dashboard') }}" class="text-xs text-indigo-600 hover:text-indigo-800">Limpar</a>
                            @endif
                        </div>
                        <form id="dashboard-tags-form" method="GET" action="{{ route('dashboard') }}">
                            <input type="hidden" id="selected-tags" name="tags" value="{{ implode(',', $selectedTags) }}">
                            <div class="flex flex-wrap gap-2 mb-3">
                                @foreach($tags as $tag)
                                    @if($tag !== 'all')
                                        <button type="button"
                                                class="tag-button-left inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold {{ in_array($tag, $selectedTags) ? 'bg-gray-300 text-gray-900 border border-gray-400' : ($tagColors[$tag] ?? 'bg-gray-200 text-gray-700') }}"
                                                data-tag="{{ $tag }}"
                                                data-original-styles="{{ $tagColors[$tag] ?? 'bg-gray-200 text-gray-700' }}">
                                            #{{ $tag }}
                                        </button>
                                    @endif
                                @endforeach
                            </div>
                            <p class="text-xs text-gray-500">Clique para filtrar o feed.</p>
                        </form>
                    </div>
                </aside>

                <!-- Feed central -->
                <main class="md:w-2/4 space-y-6">
                    <div id="composer" class="bg-white shadow-sm rounded-lg border border-gray-100 p-4 {{ $shouldOpenComposer ? '' : 'hidden' }}">
                        <x-post-form :tags="$tags" :selectedTags="$selectedTags" :hideTags="false" :memberLabs="$memberLabs" />
                    </div>

                    <div class="bg-white shadow-sm rounded-lg border border-gray-100 p-4">
                        <x-post-list :posts="$posts" :tags="$tags" :selectedTags="$selectedTags" />
                    </div>
                </main>

                <!-- Coluna direita placeholder -->
                <aside class="hidden md:block md:w-1/4 md:sticky md:top-5 self-start">
                    <div class="bg-white shadow-sm rounded-lg border border-gray-100 p-4">
                        <h3 class="text-lg font-semibold text-gray-800 mb-2">Em breve</h3>
                        <p class="text-sm text-gray-600">Aqui teremos destaques, recomendações e atalhos úteis.</p>
                    </div>
                </aside>
            </div>
        </div>
    </div>

    <!-- Navegação inferior mobile -->
    <div class="fixed bottom-0 inset-x-0 bg-white border-t border-gray-200 shadow-md flex md:hidden">
        <a href="#composer" class="w-1/2 text-center py-3 text-sm font-semibold text-indigo-600 hover:bg-indigo-50">
            Postar
        </a>
        <a href="#tags" class="w-1/2 text-center py-3 text-sm font-semibold text-gray-700 hover:bg-gray-50" onclick="document.getElementById('tags-section')?.scrollIntoView({behavior:'smooth'});">
            Tags
        </a>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const form = document.getElementById('dashboard-tags-form');
            if (!form) return;
            const hiddenInput = document.getElementById('selected-tags');
            const selectedClasses = ['bg-gray-300', 'text-gray-900', 'border', 'border-gray-400'];

            function applyStyles(selected) {
                document.querySelectorAll('.tag-button-left').forEach(btn => {
                    const original = btn.getAttribute('data-original-styles') || '';
                    const tag = btn.getAttribute('data-tag');
                    btn.className = `tag-button-left inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold`;
                    original.split(' ').forEach(cls => { if (cls) btn.classList.add(cls); });
                    if (selected.includes(tag)) {
                        selectedClasses.forEach(cls => btn.classList.add(cls));
                    }
                });
            }

            function submitSelected(selected) {
                hiddenInput.value = selected.join(',');
                form.submit();
            }

            document.querySelectorAll('.tag-button-left').forEach(btn => {
                btn.addEventListener('click', () => {
                    let current = hiddenInput.value ? hiddenInput.value.split(',').filter(Boolean) : [];
                    const tag = btn.getAttribute('data-tag');
                    if (current.includes(tag)) {
                        current = current.filter(t => t !== tag);
                    } else if (current.length < 3) {
                        current.push(tag);
                    }
                    applyStyles(current);
                    submitSelected(current);
                });
            });

            // Composer toggle
            const composer = document.getElementById('composer');
            document.querySelectorAll('[data-open-composer]').forEach(btn => {
                btn.addEventListener('click', () => {
                    if (composer) {
                        composer.classList.remove('hidden');
                        composer.scrollIntoView({ behavior: 'smooth', block: 'start' });
                    }
                });
            });

            // Abrir composer se houver erros de validação
            const shouldOpen = {{ $shouldOpenComposer ? 'true' : 'false' }};
            if (shouldOpen && composer) {
                composer.classList.remove('hidden');
            }
        });
    </script>
</x-app-layout>
