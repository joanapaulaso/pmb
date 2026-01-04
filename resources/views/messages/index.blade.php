<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Mensagens') }}
        </h2>
    </x-slot>

    @php
        $startNew = request()->boolean('new') || (!$selectedUserId && ($errors->any() || old('recipient_id') || old('body')));
        $currentContact = $recipients->firstWhere('id', $selectedUserId) ?? $conversationUsers->firstWhere('id', $selectedUserId);
        $forceList = request()->boolean('list');
        $showListMobile = $forceList || (($isMobile ?? false) && !$selectedUserId && !$startNew);
    @endphp

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6 flex flex-col md:flex-row md:space-x-6">
                    <!-- Lista de conversas -->
                    <div class="bg-gray-50 border border-gray-200 rounded-lg p-4 w-full md:w-2/5 {{ $showListMobile ? 'block' : 'hidden md:block' }}">
                        <div class="flex items-center justify-between mb-4">
                            <div>
                                <h3 class="text-lg font-medium text-gray-900">Conversas</h3>
                                <p class="text-xs text-gray-500">Total: {{ $conversationUsers->count() }}</p>
                            </div>
                            <a href="{{ route('messages.index', ['new' => 1]) }}" class="inline-flex items-center px-3 py-2 bg-indigo-600 text-white text-xs font-semibold rounded-md hover:bg-indigo-700 transition">
                                Nova mensagem
                            </a>
                        </div>
                        <div class="space-y-2 max-h-[600px] overflow-y-auto divide-y divide-gray-200">
                            @forelse($conversationUsers as $contact)
                                @php $unread = $unreadCounts[$contact->id] ?? 0; @endphp
                                <a href="{{ route('messages.index', ['user' => $contact->id]) }}"
                                   class="block px-3 py-3 hover:bg-gray-100 rounded-md {{ $selectedUserId == $contact->id ? 'bg-indigo-50 border border-indigo-200' : '' }}">
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <p class="text-sm font-semibold text-gray-900">{{ $contact->name }}</p>
                                            <p class="text-xs text-gray-500">{{ $contact->email }}</p>
                                        </div>
                                        @if($unread > 0)
                                            <span class="ml-2 inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-700">{{ $unread }}</span>
                                        @endif
                                    </div>
                                </a>
                            @empty
                                <p class="text-sm text-gray-500">Nenhuma conversa ainda.</p>
                            @endforelse
                        </div>
                    </div>

                    <!-- Thread / composer -->
                    <div class="bg-gray-50 border border-gray-200 rounded-lg p-4 flex flex-col w-full md:w-3/5 {{ $showListMobile ? 'hidden md:flex' : 'flex md:flex' }}">
                        @if($startNew)
                            <div class="flex items-center mb-4 md:hidden">
                                <a href="{{ route('messages.index', ['list' => 1]) }}" class="text-indigo-600 hover:text-indigo-800 text-sm">&larr; Voltar</a>
                            </div>
                            <div class="border border-gray-200 rounded-lg bg-white p-4">
                                <h3 class="text-lg font-medium text-gray-900 mb-3">Nova mensagem</h3>
                                <form method="POST" action="{{ route('messages.store') }}" class="space-y-3">
                                    @csrf
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Destinatário</label>
                                        <livewire:search-select
                                            model="users"
                                            field="recipient_id"
                                            placeholder="Digite para buscar usuário..."
                                            :initialValue="old('recipient_id')"
                                            :exclude-current-user="true"
                                        />
                                        @error('recipient_id')
                                            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Mensagem</label>
                                        <textarea name="body" rows="4" class="w-full border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 px-3 py-2" placeholder="Escreva sua mensagem...">{{ old('body') }}</textarea>
                                        @error('body')
                                            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    <div class="text-right">
                                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white text-xs font-semibold rounded-md hover:bg-indigo-700 transition">
                                            Enviar
                                        </button>
                                    </div>
                                </form>
                            </div>
                        @elseif($selectedUserId && $currentContact)
                            <div class="flex items-center justify-between mb-4 md:hidden">
                                <a href="{{ route('messages.index', ['list' => 1]) }}" class="text-indigo-600 hover:text-indigo-800 text-sm">&larr; Voltar</a>
                            </div>
                            <div class="flex items-center justify-between mb-4">
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-900">
                                        <a href="{{ route('public.profile', $currentContact->id) }}" class="text-indigo-600 hover:text-indigo-800">{{ $currentContact->name }}</a>
                                    </h3>
                                    <p class="text-xs text-gray-500">{{ $currentContact->email }}</p>
                                </div>
                                <div class="flex items-center space-x-2 text-xs text-gray-500">
                                    <a href="{{ route('messages.index', ['new' => 1]) }}" class="text-indigo-600 hover:text-indigo-800">Nova mensagem</a>
                                </div>
                            </div>

                            <div class="flex-1 overflow-y-auto space-y-3 mb-4 max-h-[520px] pr-1">
                                @forelse($thread as $message)
                                    <div class="flex {{ $message->sender_id === auth()->id() ? 'justify-end' : 'justify-start' }}">
                                        <div class="max-w-[80%] rounded-2xl px-3 py-2 {{ $message->sender_id === auth()->id() ? 'bg-indigo-100 text-gray-900' : 'bg-white border border-gray-200 text-gray-900' }}">
                                            <div class="text-[11px] text-gray-500 mb-1">
                                                {{ $message->sender->name }} • {{ $message->created_at->format('d/m/Y H:i') }}
                                            </div>
                                            <div class="text-sm whitespace-pre-line">{{ $message->body }}</div>
                                            @if($message->sender_id === auth()->id())
                                                <div class="text-[10px] text-gray-500 mt-1">{{ $message->read_at ? 'Lida' : 'Enviada' }}</div>
                                            @endif
                                        </div>
                                    </div>
                                @empty
                                    <p class="text-sm text-gray-500">Sem mensagens ainda. Envie a primeira!</p>
                                @endforelse
                            </div>

                            <div class="border border-gray-200 rounded-lg bg-white p-3">
                                <form method="POST" action="{{ route('messages.store') }}" class="flex items-center space-x-2">
                                    @csrf
                                    <input type="hidden" name="recipient_id" value="{{ $selectedUserId }}">
                                    <textarea name="body" rows="2" class="flex-1 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 px-3 py-2" placeholder="Escreva sua mensagem...">{{ old('body') }}</textarea>
                                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white text-xs font-semibold rounded-md hover:bg-indigo-700 transition">
                                        Enviar
                                    </button>
                                </form>
                                @error('body')
                                    <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        @else
                            <div class="text-center text-sm text-gray-500">
                                Selecione uma conversa à esquerda ou <a href="{{ route('messages.index', ['new' => 1]) }}" class="text-indigo-600 hover:text-indigo-800">inicie uma nova</a>.
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
