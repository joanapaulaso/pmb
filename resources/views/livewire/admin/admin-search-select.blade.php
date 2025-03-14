<div
    wire:key="{{ $field }}-wrapper"
    x-data="{
        open: false,
        search: @entangle('search')
    }"
    class="relative"
>
    <input
        type="text"
        x-model="search"
        wire:model.live.debounce.300ms="search"
        @focus="open = true"
        @click.outside="open = false"
        @keydown.escape.window="open = false"
        placeholder="{{ $placeholder }}"
        class="shadow-sm focus:ring-green-500 focus:border-green-500 block w-full sm:text-sm border-gray-300 rounded-md"
    >
    <input type="hidden" name="{{ $field }}" value="{{ $selectedId }}">

    <!-- Results dropdown -->
    @if(count($results) > 0)
        <div
            x-show="open"
            x-transition
            class="absolute z-50 w-full mt-1 bg-white border rounded-md shadow-lg max-h-60 overflow-auto"
        >
            @foreach($results as $result)
                <div
                    wire:key="{{ $field }}-result-{{ $loop->index }}"
                    wire:click="selectOption('{{ $result->id }}', '{{ addslashes($result->name) }}')"
                    @click="open = false"
                    class="px-4 py-2 hover:bg-gray-100 cursor-pointer"
                >
                    {{ $result->name }}
                </div>
            @endforeach
        </div>
    @endif
</div>
