<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Portal') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <!-- Formulário de nova postagem -->
                    <form method="POST" action="{{ route('portal.store') }}" enctype="multipart/form-data" id="post-form">
                        @csrf
                        <div class="mb-4">
                            <div class="relative">
                                <div id="editor" class="border rounded-lg" style="min-height: 150px;"></div>
                                <input type="hidden" name="content" id="content">
                                @error('content')
                                    <span class="text-red-500 text-sm">{{ $message }}</span>
                                @enderror
                                <div id="debug-content" class="text-sm text-gray-600" style="display: none;"></div>
                            </div>
                            <div class="mt-2 flex items-center space-x-4">
                                <input type="file" name="media" accept="image/jpeg,image/png,video/mp4" class="text-sm">
                                @error('media')
                                    <span class="text-red-500 text-sm">{{ $message }}</span>
                                @enderror
                                <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">Publicar</button>
                            </div>
                        </div>
                    </form>

                    <!-- Lista de postagens -->
                    @if($posts->isEmpty())
                        <p>Nenhum post encontrado.</p>
                    @else
                        @foreach($posts as $post)
                            <div class="border-b py-4">
                                <div class="flex justify-between items-start">
                                    <div class="flex-1">
                                        <div class="flex items-center space-x-2">
                                            <span class="font-semibold">{{ $post->user->name }}</span>
                                            <span class="text-gray-500 text-sm">{{ $post->created_at->diffForHumans() }}</span>
                                        </div>
                                        <div class="mt-2 post-content">{!! $post->content !!}</div>
                                        @if($post->media)
                                            @if($post->media_type === 'image')
                                                <img src="{{ Storage::url($post->media) }}" class="mt-2 max-w-full h-auto rounded">
                                            @elseif($post->media_type === 'video')
                                                <video controls class="mt-2 max-w-full rounded">
                                                    <source src="{{ Storage::url($post->media) }}" type="video/mp4">
                                                </video>
                                            @endif
                                        @endif
                                    </div>
                                    @if(auth()->user()->id === $post->user_id)
                                        <form action="{{ route('portal.pin', $post) }}" method="POST">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="text-gray-500 hover:text-gray-700">
                                                <i class="fas fa-thumbtack {{ $post->pinned ? 'text-blue-500' : '' }}"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    @endif
                </div>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('post-form').addEventListener('submit', (event) => {
            const contentInput = document.getElementById('content');
            const debugContent = document.getElementById('debug-content');
            if (contentInput && debugContent) {
                debugContent.style.display = 'block';
                debugContent.textContent = 'Conteúdo: ' + contentInput.value;
            }
            console.log('Submit manual disparado no script inline!');
        });
    </script>
</x-app-layout>
