<div class="relative">
    <input
        type="text"
        wire:model.live.debounce.500ms="search"
        class="w-full px-4 py-2 border-1 border-gray-300 rounded text-gray-700 placeholder-gray-400 focus:ring-2 focus:ring-blue-500 focus:outline-transparent transition-colors"
        placeholder="{{ $placeholder }}"
    >

    @if($search && !$selectedId && $results->count() > 0)
        <div class="absolute z-10 w-full mt-1 bg-white border-1 border-gray-200 rounded shadow-xl max-h-60 overflow-auto">
            @foreach($results as $result)
                <div
                    wire:click.prevent="selectOption('{{ $result->$primaryKey }}')"
                    class="px-4 py-2 text-gray-700 hover:bg-gray-200 cursor-pointer transition-colors"
                >
                    {{ $result->name }}
                </div>
            @endforeach
        </div>
    @endif
</div>