@props(['users', 'sort', 'direction'])

<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
            <table class="min-w-full leading-normal">
                <thead>
                    <tr>
                        <th scope="col" class="px-5 py-3 bg-gray-100 border-b border-gray-200 text-gray-800 text-left text-sm uppercase font-normal">
                            <div class="flex items-center">
                                <span>Nome</span>
                                <div class="ml-1 flex flex-col">
                                    <a href="{{ route('membros', ['sort' => 'name', 'direction' => 'asc']) }}" class="{{ ($sort == 'name' && $direction == 'asc') ? 'text-blue-600' : 'text-gray-400' }}">
                                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M14.707 12.707a1 1 0 01-1.414 0L10 9.414l-3.293 3.293a1 1 0 01-1.414-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 010 1.414z" clip-rule="evenodd" />
                                        </svg>
                                    </a>
                                    <a href="{{ route('membros', ['sort' => 'name', 'direction' => 'desc']) }}" class="{{ ($sort == 'name' && $direction == 'desc') ? 'text-blue-600' : 'text-gray-400' }}">
                                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                        </svg>
                                    </a>
                                </div>
                            </div>
                        </th>
                        <th scope="col" class="px-5 py-3 bg-gray-100 border-b border-gray-200 text-gray-800 text-left text-sm uppercase font-normal">
                            <div class="flex items-center">
                                <span>Laboratório</span>
                                <div class="ml-1 flex flex-col">
                                    <a href="{{ route('membros', ['sort' => 'laboratory', 'direction' => 'asc']) }}" class="{{ ($sort == 'laboratory' && $direction == 'asc') ? 'text-blue-600' : 'text-gray-400' }}">
                                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M14.707 12.707a1 1 0 01-1.414 0L10 9.414l-3.293 3.293a1 1 0 01-1.414-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 010 1.414z" clip-rule="evenodd" />
                                        </svg>
                                    </a>
                                    <a href="{{ route('membros', ['sort' => 'laboratory', 'direction' => 'desc']) }}" class="{{ ($sort == 'laboratory' && $direction == 'desc') ? 'text-blue-600' : 'text-gray-400' }}">
                                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                        </svg>
                                    </a>
                                </div>
                            </div>
                        </th>
                        <th scope="col" class="px-5 py-3 bg-gray-100 border-b border-gray-200 text-gray-800 text-left text-sm uppercase font-normal">
                            <div class="flex items-center">
                                <span>Instituição</span>
                                <div class="ml-1 flex flex-col">
                                    <a href="{{ route('membros', ['sort' => 'institution', 'direction' => 'asc']) }}" class="{{ ($sort == 'institution' && $direction == 'asc') ? 'text-blue-600' : 'text-gray-400' }}">
                                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M14.707 12.707a1 1 0 01-1.414 0L10 9.414l-3.293 3.293a1 1 0 01-1.414-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 010 1.414z" clip-rule="evenodd" />
                                        </svg>
                                    </a>
                                    <a href="{{ route('membros', ['sort' => 'institution', 'direction' => 'desc']) }}" class="{{ ($sort == 'institution' && $direction == 'desc') ? 'text-blue-600' : 'text-gray-400' }}">
                                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                        </svg>
                                    </a>
                                </div>
                            </div>
                        </th>
                        <th scope="col" class="px-5 py-3 bg-gray-100 border-b border-gray-200 text-gray-800 text-left text-sm uppercase font-normal">
                            <div class="flex items-center">
                                <span>Estado</span>
                                <div class="ml-1 flex flex-col">
                                    <a href="{{ route('membros', ['sort' => 'state', 'direction' => 'asc']) }}" class="{{ ($sort == 'state' && $direction == 'asc') ? 'text-blue-600' : 'text-gray-400' }}">
                                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M14.707 12.707a1 1 0 01-1.414 0L10 9.414l-3.293 3.293a1 1 0 01-1.414-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 010 1.414z" clip-rule="evenodd" />
                                        </svg>
                                    </a>
                                    <a href="{{ route('membros', ['sort' => 'state', 'direction' => 'desc']) }}" class="{{ ($sort == 'state' && $direction == 'desc') ? 'text-blue-600' : 'text-gray-400' }}">
                                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                        </svg>
                                    </a>
                                </div>
                            </div>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($users as $user)
                        <tr>
                            <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                <div class="flex items-center">
                                    <div class="ml-3">
                                        <p class="text-gray-900 whitespace-no-wrap">
                                            <a href="{{ route('public.profile', $user) }}" class="text-blue-500 hover:underline">
                                                {{ $user->name }}
                                            </a>
                                        </p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                <p class="text-gray-900 whitespace-no-wrap">
                                    @if($user->profile && $user->profile->laboratory)
                                        {{ $user->profile->laboratory->name }}
                                    @else
                                        @forelse ($user->teams as $team)
                                            {{ $team->name }}
                                            @if(!$loop->last), @endif
                                        @empty
                                            {{ __('Não informado') }}
                                        @endforelse
                                    @endif
                                </p>
                            </td>
                            <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                <p class="text-gray-900 whitespace-no-wrap">
                                    @if($user->profile && $user->profile->institution)
                                        {{ $user->profile->institution->name }}
                                    @else
                                        {{ __('Não informado') }}
                                    @endif
                                </p>
                            </td>
                            <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                <p class="text-gray-900 whitespace-no-wrap">
                                    @if($user->profile && $user->profile->state)
                                        {{ $user->profile->state->name }}
                                    @else
                                        {{ __('Não informado') }}
                                    @endif
                                </p>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                <p class="text-gray-900 whitespace-no-wrap">{{ __('Nenhum membro encontrado.') }}</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <div class="mt-4">
                {{ $users->links() }}
            </div>
        </div>
    </div>
</div>
