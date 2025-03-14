@extends('admin.layouts.admin-layout')

@section('page-title', 'Gerenciar Laboratórios')

@section('content')
<div class="flex flex-col space-y-3 mb-6">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between space-y-3 md:space-y-0">
        <a href="{{ route('admin.laboratories.create') }}" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 active:bg-green-900 focus:outline-none focus:border-green-900 focus:ring focus:ring-green-300 disabled:opacity-25 transition">
            <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
            </svg>
            Adicionar Laboratório
        </a>
    </div>

    <!-- Filtro de time_id -->
    <div class="bg-white p-4 rounded-lg shadow-sm">
        <form action="{{ route('admin.laboratories.index') }}" method="GET" class="flex items-center space-x-4">
            <div class="flex items-center">
                <label for="has_team" class="block text-sm font-medium text-gray-700 mr-2">Exibir:</label>
                <select id="has_team" name="has_team" onchange="this.form.submit()" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                    <option value="1" {{ $hasTeam == '1' ? 'selected' : '' }}>Apenas laboratórios com equipe</option>
                    <option value="0" {{ $hasTeam == '0' ? 'selected' : '' }}>Apenas laboratórios sem equipe</option>
                    <option value="all" {{ ($hasTeam != '0' && $hasTeam != '1') ? 'selected' : '' }}>Todos os laboratórios</option>
                </select>
            </div>
        </form>
    </div>
</div>

<div class="bg-white shadow-md rounded-lg overflow-hidden">
    <div class="bg-gray-50 px-6 py-4 border-b border-gray-200">
        <h2 class="text-lg font-medium text-gray-900">Laboratórios</h2>
    </div>

    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Laboratório
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Instituição
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Estado
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Equipe
                    </th>
                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Ações
                    </th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($laboratories as $laboratory)
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center">
                            @if($laboratory->logo)
                            <div class="flex-shrink-0 h-10 w-10">
                                <img class="h-10 w-10 rounded-full object-cover" src="{{ asset('storage/' . $laboratory->logo) }}" alt="{{ $laboratory->name }}">
                            </div>
                            @else
                            <div class="flex-shrink-0 h-10 w-10 bg-green-100 rounded-full flex items-center justify-center">
                                <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path>
                                </svg>
                            </div>
                            @endif
                            <div class="ml-4">
                                <div class="text-sm font-medium text-gray-900">{{ $laboratory->name }}</div>
                                @if($laboratory->website)
                                <a href="{{ $laboratory->website }}" class="text-xs text-blue-600 hover:text-blue-900" target="_blank">{{ $laboratory->website }}</a>
                                @endif
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-900">{{ $laboratory->institution ? $laboratory->institution->name : '-' }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-900">{{ $laboratory->state ? $laboratory->state->name : '-' }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @if($laboratory->team_id)
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                Sim
                            </span>
                        @else
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                Não
                            </span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                        <a href="{{ route('admin.laboratories.edit', $laboratory) }}" class="text-indigo-600 hover:text-indigo-900 mr-3">Editar</a>

                        <form action="{{ route('admin.laboratories.destroy', $laboratory) }}" method="POST" class="inline" onsubmit="return confirm('Tem certeza que deseja excluir este laboratório? Esta ação irá remover o time/equipe associado e não pode ser desfeita.');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 hover:text-red-900">Excluir</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-4 text-center text-sm text-gray-500">
                        Nenhum laboratório encontrado.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="px-6 py-4 border-t border-gray-200">
        {{ $laboratories->appends(['has_team' => $hasTeam])->links() }}
    </div>
</div>
@endsection
