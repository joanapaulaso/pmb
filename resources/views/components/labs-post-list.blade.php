@props(['posts', 'tags', 'selectedTags'])

@php
    $tagColors = config('tags.colors'); // Use colors from config
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
                    @if($post->tag)
                        <span class="inline-block {{ $tagColors[$post->tag] ?? 'bg-gray-200 text-gray-700' }} rounded-full px-3 py-1 text-xs font-semibold">
                            #{{ $post->tag }}
                        </span>
                    @endif
                    @if($post->additional_tags && is_array($post->additional_tags))
                        @foreach($post->additional_tags as $tag)
                            <span class="inline-block {{ $tagColors[$tag] ?? 'bg-gray-200 text-gray-700' }} rounded-full px-3 py-1 text-xs font-semibold">
                                #{{ $tag }}
                            </span>
                        @endforeach
                    @endif
                </div>

                <!-- Render HTML content -->
                <div class="post-content prose max-w-none">{!! $post->content !!}</div>

                @if (!empty($post->metadata))
                    <div class="mt-4 border rounded flex overflow-hidden">
                        <a href="{{ $post->metadata['url'] }}" target="_blank" class="flex w-full">
                            @if (!empty($post->metadata['image']))
                                <img src="{{ $post->metadata['image'] }}" alt="{{ $post->metadata['title'] }}" class="w-24 h-auto object-cover">
                            @endif
                            <div class="p-4 flex-grow">
                                <h3 class="font-semibold text-lg">{{ $post->metadata['title'] }}</h3>
                                <p>{{ $post->metadata['description'] }}</p>
                            </div>
                        </a>
                    </div>
                @endif

                <p class="text-sm text-gray-500">{{ $post->created_at->diffForHumans() }}</p>

                @can('delete', $post)
                    <form action="{{ route('posts.destroy', $post) }}" method="POST" class="inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-red-500">Deletar</button>
                    </form>
                @endcan

                <!-- Replies -->
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
