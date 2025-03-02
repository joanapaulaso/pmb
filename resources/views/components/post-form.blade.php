@props(['tags'])

@php
    $tagColors = [
        'general' => 'bg-gray-200 text-gray-700',
        'question' => 'bg-red-200 text-red-700',
        'job' => 'bg-blue-200 text-blue-700',
        'promotion' => 'bg-green-200 text-green-700',
        'idea' => 'bg-pink-200 text-pink-700',
        'collaboration' => 'bg-purple-200 text-purple-700',
        'paper' => 'bg-yellow-200 text-yellow-700',
        'news' => 'bg-cyan-200 text-cyan-700',
    ];
@endphp

<div x-data="dropdown()">
    @if (isset($post))
        <form action="{{ route('posts.reply', $post) }}" method="POST">
            @csrf
            <textarea name="content" rows="2" class="w-full border rounded p-2" placeholder="Reply to this post"></textarea>
            <button type="submit" class="mt-2 px-4 py-2 bg-blue-500 text-white rounded">Reply</button>
        </form>
    @else
        <form action="{{ route('posts.store') }}" method="POST">
            @csrf
            <textarea name="content" rows="2" class="w-full border rounded p-2 mb-2" placeholder="Write your post"></textarea>
            <div class="mb-2 relative">
                <label for="tag" class="block text-gray-700 text-sm font-bold mb-2">Select a tag:</label>
                <div @click="toggle()" class="w-full border rounded p-2 cursor-pointer bg-white flex items-center justify-between" :class="{ 'border-blue-500': open, 'border-gray-300': !open }">
                    <span x-text="selectedTag ? selectedTag : 'Choose a tag'"></span>
                    <svg :class="{'rotate-180': open}" class="w-4 h-4 transform transition-transform duration-200" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </div>
                <div x-show="open" @click.away="close()" class="absolute mt-1 w-full rounded bg-white shadow-lg z-10 overflow-y-auto max-h-48">
                    @foreach($tags as $tag)
                        <div @click="select('{{ $tag }}')" class="p-2 cursor-pointer {{ $tagColors[$tag] ?? 'bg-gray-200 text-gray-700' }} hover:bg-gray-300">
                            #{{ $tag }}
                        </div>
                    @endforeach
                </div>
            </div>
            <input type="hidden" name="tag" x-model="selectedTag">
            <button type="submit" class="mt-2 px-4 py-2 bg-blue-500 text-white rounded">Post</button>
        </form>
    @endif
</div>

<script>
    function dropdown() {
        return {
            open: false,
            selectedTag: '',
            toggle() {
                this.open = !this.open;
            },
            close() {
                this.open = false;
            },
            select(tag) {
                this.selectedTag = tag;
                this.close();
            },
        }
    }
</script>
