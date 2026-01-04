@props(['posts', 'tags', 'selectedTags'])

@php
    $postItems = $posts instanceof \Illuminate\Contracts\Pagination\Paginator
        ? $posts->items()
        : $posts;
    $posts = collect($postItems)->sortByDesc('created_at');
    $tagColors = config('tags.colors');
    if (!isset($selectedTags)) {
        $selectedTags = [];
    }
    if (!is_array($selectedTags) && is_string($selectedTags)) {
        $selectedTags = explode(',', $selectedTags);
    }
    $selectedTags = is_array($selectedTags) ? array_filter($selectedTags) : [];
@endphp

<div>
    <div id="posts-container">
        @foreach($posts as $post)
            <div class="mb-8 p-4 border border-gray-100 rounded" id="post-{{ $post->id }}">
                <p class="font-bold">
                    <a href="{{ route('public.profile', $post->user) }}" class="text-blue-500 hover:underline">
                        {{ $post->user->name }}
                    </a>
                </p>

        <!-- Display all tags -->
        <div class="flex flex-wrap gap-2 mb-2">
            @if(isset($post->tag))
                <span class="inline-block {{ $tagColors[$post->tag] ?? 'bg-gray-200 text-gray-700' }} rounded-full px-3 py-1 text-xs font-semibold">
                    #{{ $post->tag }}
                </span>
            @endif
            @if(isset($post->additional_tags) && is_array($post->additional_tags))
                @foreach($post->additional_tags as $tag)
                    <span class="inline-block {{ $tagColors[$tag] ?? 'bg-gray-200 text-gray-700' }} rounded-full px-3 py-1 text-xs font-semibold">
                        #{{ $tag }}
                    </span>
                @endforeach
            @elseif(isset($post->all_tags) && is_array($post->all_tags))
                @foreach($post->all_tags as $tag)
                    @if($tag !== $post->tag)
                        <span class="inline-block {{ $tagColors[$tag] ?? 'bg-gray-200 text-gray-700' }} rounded-full px-3 py-1 text-xs font-semibold">
                            #{{ $tag }}
                        </span>
                    @endif
                @endforeach
            @endif
        </div>

        <!-- Render HTML content from Quill -->
        <div class="post-content prose max-w-none">{!! $post->content !!}</div>

        <p class="text-sm text-gray-500">{{ $post->created_at->diffForHumans() }}</p>

        @can('delete', $post)
            <form action="{{ route('posts.destroy', $post) }}" method="POST" class="inline">
                @csrf
                @method('DELETE')
                <button type="submit" class="text-red-500">Deletar</button>
            </form>
        @endcan

        <div x-data="{ showReply: false, replying: false }">
            <button @click="showReply = !showReply" class="text-blue-500">Responder</button>
            <div x-show="showReply" class="mt-2">
                <form @submit.prevent="submitReply($event, '{{ route('posts.reply', $post) }}', {{ $post->id }})">
                    @csrf
                    <div class="reply-quill-container mb-2">
                        <div class="reply-quill-toolbar-{{ $post->id }}">
                            <span class="ql-formats">
                                <button class="ql-bold"></button>
                                <button class="ql-italic"></button>
                                <button class="ql-underline"></button>
                            </span>
                            <span class="ql-formats">
                                <button class="ql-link"></button>
                            </span>
                        </div>
                        <div id="reply-quill-editor-{{ $post->id }}" class="border rounded p-2" style="min-height: 80px;"></div>
                        <input type="hidden" name="content" class="reply-content-input">
                    </div>
                    <button type="submit" class="mt-2 px-4 py-2 bg-blue-500 text-white rounded" :disabled="replying">Reply</button>
                </form>
            </div>
        </div>

        <div class="replies-container ml-8 mt-4 border-l-2 border-gray-200 pl-4">
            @foreach($post->replies as $reply)
                <div class="mb-4">
                    <p class="font-bold">
                        <a href="{{ route('public.profile', $reply->user) }}" class="text-blue-500 hover:underline">
                            {{ $reply->user->name }}
                        </a>
                    </p>
                    <div class="reply-content prose max-w-none">{!! $reply->content !!}</div>

                    @if (!empty($reply->metadata) && !empty($reply->metadata['url']) && !empty($reply->metadata['title']))
                        <div class="mt-4 border rounded flex overflow-hidden">
                            <a href="{{ $reply->metadata['url'] }}" target="_blank" class="flex w-full">
                                @if (!empty($reply->metadata['image']))
                                    <img src="{{ $reply->metadata['image'] }}" alt="{{ $reply->metadata['title'] }}" class="w-24 h-auto object-cover">
                                @endif
                                <div class="p-4 flex-grow">
                                    <h3 class="font-semibold text-lg">{{ $reply->metadata['title'] }}</h3>
                                    @if (!empty($reply->metadata['description']))
                                        <p>{{ $reply->metadata['description'] }}</p>
                                    @endif
                                </div>
                            </a>
                        </div>
                    @endif
                    <p class="text-sm text-gray-500">{{ $reply->created_at->diffForHumans() }}</p>
                    @can('delete', $reply)
                        <form action="{{ route('replies.destroy', $reply) }}" method="POST" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-500">Delete</button>
                        </form>
                    @endcan
                </div>
            @endforeach
        </div>
            </div>
        @endforeach
    </div>
</div>

<style>
    .post-content a {
        color: #2563eb;
        font-weight: 600;
        text-decoration: none;
    }
    .post-content a:hover {
        text-decoration: underline;
    }
</style>
