<!-- In resources/views/components/post-list.blade.php -->
@props(['posts', 'tags', 'selectedTags'])

@php
    $posts = $posts->sortByDesc('created_at');

    $tagColors = [
        'all' => 'bg-gray-200 text-gray-700',
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

<div>
    <form action="{{ route('dashboard') }}" method="GET" class="mb-4" x-data="tagFilter()">
        <div class="flex flex-wrap gap-2">
            @foreach($tags as $tag)
                <button type="button"
                        class="tag-button inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold
                        {{ in_array($tag, $selectedTags) ? 'bg-gray-800 text-white' : $tagColors[$tag] }}"
                        data-tag="{{ $tag }}"
                        data-original-styles="{{ $tagColors[$tag] }}"
                        @click="toggleTag('{{ $tag }}')">
                    #{{ $tag }}
                </button>
            @endforeach
        </div>
        <input type="hidden" name="tags" id="selected-tags" x-model="selectedTagsString">
        <button type="submit" class="mt-2 px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">
            Filter Posts
        </button>
    </form>

    @foreach($posts as $post)
        <div class="mb-8 p-4 border rounded" id="post-{{ $post->id }}">
            <p class="font-bold">
                <a href="{{ route('public.profile', $post->user) }}" class="text-blue-500 hover:underline">
                    {{ $post->user->name }}
                </a>
            </p>

            <!-- Display all tags -->
            <div class="flex flex-wrap gap-2 mb-2">
                @foreach($post->all_tags as $tag)
                    <span class="inline-block {{ $tagColors[$tag] ?? 'bg-gray-200 text-gray-700' }} rounded-full px-3 py-1 text-xs font-semibold">
                        #{{ $tag }}
                    </span>
                @endforeach
            </div>
            <p>{{ $post->content }}</p>
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
                    <button type="submit" class="text-red-500">Delete</button>
                </form>
            @endcan

            <div x-data="replyForm('{{ route('posts.reply', $post) }}', {{ $post->id }})">
                <button @click="showReply = !showReply" class="text-blue-500">Reply</button>
                <div x-show="showReply" class="mt-2">
                    <form @submit.prevent="submitReply">
                        @csrf
                        <textarea name="content" rows="2" class="w-full border rounded p-2" placeholder="Reply to this post"></textarea>
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
                        <p>{{ $reply->content }}</p>
                        @if (!empty($reply->metadata))
                            <div class="mt-4 border rounded flex overflow-hidden">
                                <a href="{{ $reply->metadata['url'] }}" target="_blank" class="flex w-full">
                                    @if (!empty($reply->metadata['image']))
                                        <img src="{{ $reply->metadata['image'] }}" alt="{{ $reply->metadata['title'] }}" class="w-24 h-auto object-cover">
                                    @endif
                                    <div class="p-4 flex-grow">
                                        <h3 class="font-semibold text-lg">{{ $reply->metadata['title'] }}</h3>
                                        <p>{{ $reply->metadata['description'] }}</p>
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

<script>
function replyForm(actionUrl, postId) {
    return {
        showReply: false,
        replying: false,
        submitted: false,
        async submitReply(event) {
            event.preventDefault();
            if (this.replying || this.submitted) {
                console.log('Preventing duplicate submission');
                return;
            }
            this.replying = true;
            this.submitted = true;

            const form = event.target;

            try {
                const response = await fetch(actionUrl, {
                    method: 'POST',
                    body: new FormData(form),
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json'
                    }
                });

                if (!response.ok) {
                    throw new Error('Network response was not ok.');
                }

                const data = await response.json();
                console.log('Response data:', data);

                if (data.message === 'Reply posted successfully') {
                    this.updateUI(data.reply, postId);
                }

                form.reset();
                this.showReply = false;
            } catch (error) {
                console.error('There was a problem with the fetch operation:', error);
            } finally {
                this.replying = false;
            }
        },
        updateUI(reply, postId) {
            console.log('Updating UI with reply:', reply);
            const replyContainer = document.createElement('div');
            replyContainer.className = 'mb-4';
            replyContainer.innerHTML = `
                <p class="font-bold">
                    <a href="/profile/${reply.user.id}" class="text-blue-500 hover:underline">
                        ${reply.user.name}
                    </a>
                </p>
                <p>${reply.content}</p>
                ${reply.metadata ? `
                    <div class="mt-4 border rounded flex overflow-hidden">
                        <a href="${reply.metadata.url}" target="_blank" class="flex w-full">
                            ${reply.metadata.image ? `<img src="${reply.metadata.image}" alt="${reply.metadata.title}" class="w-24 h-auto object-cover">` : ''}
                            <div class="p-4 flex-grow">
                                <h3 class="font-semibold text-lg">${reply.metadata.title}</h3>
                                <p>${reply.metadata.description}</p>
                            </div>
                        </a>
                    </div>
                ` : ''}
                <p class="text-sm text-gray-500">${new Date(reply.created_at).toLocaleString()}</p>
            `;

            if (reply.can_delete) {
                console.log('User can delete this reply');
                const deleteForm = document.createElement('form');
                deleteForm.action = `/replies/${reply.id}`;
                deleteForm.method = 'POST';
                deleteForm.className = 'inline';
                deleteForm.innerHTML = `
                    <input type="hidden" name="_token" value="${document.querySelector('meta[name="csrf-token"]').getAttribute('content')}">
                    <input type="hidden" name="_method" value="DELETE">
                    <button type="submit" class="text-red-500">Delete</button>
                `;
                replyContainer.appendChild(deleteForm);
            } else {
                console.log('User cannot delete this reply');
            }

            const postElement = document.getElementById(`post-${postId}`);
            const repliesContainer = postElement.querySelector('.replies-container');
            repliesContainer.appendChild(replyContainer);

            replyContainer.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        },
    }
}

function toggleTag(element, tag) {
    let selectedTags = document.getElementById('selected-tags').value.split(',');

    if (selectedTags.includes(tag)) {
        selectedTags = selectedTags.filter(t => t !== tag);
        element.classList.remove('bg-gray-800', 'text-white');
        element.classList.add(...element.getAttribute('data-original-styles').split(' '));
    } else {
        selectedTags.push(tag);
        element.classList.remove(...element.getAttribute('data-original-styles').split(' '));
        element.classList.add('bg-gray-800', 'text-white');
    }

    document.getElementById('selected-tags').value = selectedTags.join(',');
}

function tagFilter() {
    return {
        selectedTags: [],
        init() {
            // Initialize with existing tags from URL
            const initialTags = document.getElementById('selected-tags').value.split(',').filter(tag => tag);
            this.selectedTags = initialTags;
        },
        toggleTag(tag) {
            if (this.selectedTags.includes(tag)) {
                this.selectedTags = this.selectedTags.filter(t => t !== tag);
            } else if (this.selectedTags.length < 3) {
                this.selectedTags.push(tag);
            }
        },
        get selectedTagsString() {
            // Limit to 3 tags
            return this.selectedTags.slice(0, 3).join(',');
        }
    }
}

document.addEventListener('DOMContentLoaded', (event) => {
    const initialTags = document.getElementById('selected-tags').value.split(',');
    document.querySelectorAll('.tag-button').forEach(button => {
        const tag = button.getAttribute('data-tag');
        if (initialTags.includes(tag)) {
            button.classList.add('bg-gray-800', 'text-white');
            button.classList.remove(...button.getAttribute('data-original-styles').split(' '));
        }
    });
});
</script>
