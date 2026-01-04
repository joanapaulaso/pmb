<div class="relative">
    <input type="hidden" name="{{ $field }}" id="{{ $field }}_hidden" wire:model="selectedId" value="{{ $selectedId }}">
    <input
        type="text"
        id="{{ $field }}_search"
        wire:model.live="search"
        wire:blur="clearIfUnselected"
        class="w-full px-4 py-2 border-1 border-gray-300 rounded text-gray-700 placeholder-gray-400 focus:ring-2 focus:ring-blue-500 focus:outline-transparent transition-colors"
        placeholder="{{ $placeholder }}"
    >

    @if($search && !$selectedId && $results->count() > 0)
        <div class="absolute z-10 w-full mt-1 bg-white border-1 border-gray-200 rounded shadow-xl max-h-60 overflow-auto">
            @foreach($results as $result)
                <div
                    wire:mousedown.prevent="selectOption('{{ $result->{$this->getPrimaryKey($this->model)} }}')"
                    class="px-4 py-2 text-gray-700 hover:bg-gray-200 cursor-pointer transition-colors"
                    data-value="{{ $result->{$this->getPrimaryKey($this->model)} }}"
                    data-label="{{ $result->name }}"
                >
                    {{ $result->name }}
                </div>
            @endforeach
        </div>
    @endif
</div>

@push('scripts')
<script>
document.addEventListener('livewire:init', () => {
    Livewire.on('optionSelected', (data) => {
        if (data?.field && data?.value !== undefined) {
            const hidden = document.getElementById(`${data.field}_hidden`);
            if (hidden) {
                hidden.value = data.value ?? '';
            }
        }
    });
});
</script>
@endpush
