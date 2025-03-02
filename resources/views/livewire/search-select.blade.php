<div class="relative">
    <input
        type="text"
        wire:model.live="search"
        class="w-full px-4 py-2 border rounded-md"
        placeholder="{{ $placeholder }}"
    >

    @if($search && !$selectedId && $results->count() > 0)
        <div class="absolute z-10 w-full mt-1 bg-white border rounded-md shadow-lg max-h-60 overflow-auto">
            @foreach($results as $result)
                <div
                    wire:click="selectOption('{{ $result->{$this->getPrimaryKey($this->model)} }}')"
                    class="px-4 py-2 hover:bg-gray-100 cursor-pointer"
                >
                    {{ $result->name }}
                </div>
            @endforeach
        </div>
    @endif
</div>
