<!-- In resources/views/components/post-list.blade.php -->
@props(['posts', 'tags', 'selectedTags'])

@php
    // Ensure posts are sorted by created_at in descending order
    $posts = collect($posts)->sortByDesc('created_at');

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

    // Initialize $selectedTags if it's null
    if (!isset($selectedTags)) {
        $selectedTags = [];
    }

    // Convert to array if it's a string
    if (!is_array($selectedTags) && is_string($selectedTags)) {
        $selectedTags = explode(',', $selectedTags);
    }

    // Filter out empty values
    $selectedTags = is_array($selectedTags) ? array_filter($selectedTags) : [];
@endphp

<div>
    <form action="{{ route('dashboard') }}" method="GET" class="mb-4">
        <div class="flex flex-wrap gap-2">
            @foreach($tags as $tag)
                <button type="button"
                        class="tag-button inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold
                        {{ in_array($tag, $selectedTags) ? 'bg-gray-800 text-white' : $tagColors[$tag] }}"
                        data-tag="{{ $tag }}"
                        data-original-styles="{{ $tagColors[$tag] }}"
                        onclick="toggleTag(this, '{{ $tag }}')">
                    #{{ $tag }}
                </button>
            @endforeach
        </div>
        <input type="hidden" name="tags" id="selected-tags" value="{{ implode(',', $selectedTags) }}">
        <button type="submit" class="mt-2 px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">
            Filter Posts
        </button>
    </form>

    @php
        if (!empty($selectedTags) && !in_array('all', $selectedTags)) {
            $filteredPosts = $posts->filter(function($post) use ($selectedTags) {
                // Create array of post tags
                $postTags = [];

                // Add primary tag if it exists
                if (isset($post->tag)) {
                    $postTags[] = $post->tag;
                }

                // Add additional tags if they exist
                if (isset($post->additional_tags) && is_array($post->additional_tags)) {
                    $postTags = array_merge($postTags, $post->additional_tags);
                } elseif (isset($post->all_tags) && is_array($post->all_tags)) {
                    $postTags = $post->all_tags;
                }

                // Check if any selected tag matches post tags
                foreach ($selectedTags as $tag) {
                    if (in_array($tag, $postTags)) {
                        return true;
                    }
                }
                return false;
            });
        } else {
            $filteredPosts = $posts;
        }
    @endphp

    @foreach($filteredPosts as $post)
        <div class="mb-8 p-4 border rounded" id="post-{{ $post->id }}">
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

            <div x-data="{ showReply: false, replying: false }">
                <button @click="showReply = !showReply" class="text-blue-500">Reply</button>
                <div x-show="showReply" class="mt-2">
                    <form @submit.prevent="submitReply($event, '{{ route('posts.reply', $post) }}', {{ $post->id }})">
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
// Global function for tag toggling
function toggleTag(element, tag) {
    const selectedTagsInput = document.getElementById('selected-tags');
    let selectedTags = (selectedTagsInput.value) ? selectedTagsInput.value.split(',') : [];

    if (tag === 'all') {
        // If "all" is clicked, clear other selections
        if (selectedTags.includes('all')) {
            selectedTags = [];
            document.querySelectorAll('.tag-button').forEach(btn => {
                btn.classList.remove('bg-gray-800', 'text-white');
                const originalStyles = btn.getAttribute('data-original-styles');
                if (originalStyles) {
                    originalStyles.split(' ').forEach(cls => {
                        if (cls) btn.classList.add(cls);
                    });
                }
            });
        } else {
            selectedTags = ['all'];
            document.querySelectorAll('.tag-button').forEach(btn => {
                const btnTag = btn.getAttribute('data-tag');
                if (btnTag === 'all') {
                    btn.classList.remove(...btn.getAttribute('data-original-styles').split(' '));
                    btn.classList.add('bg-gray-800', 'text-white');
                } else {
                    btn.classList.remove('bg-gray-800', 'text-white');
                    const originalStyles = btn.getAttribute('data-original-styles');
                    if (originalStyles) {
                        originalStyles.split(' ').forEach(cls => {
                            if (cls) btn.classList.add(cls);
                        });
                    }
                }
            });
        }
    } else {
        // Remove "all" if it's selected
        if (selectedTags.includes('all')) {
            selectedTags = selectedTags.filter(t => t !== 'all');
            const allBtn = document.querySelector('.tag-button[data-tag="all"]');
            if (allBtn) {
                allBtn.classList.remove('bg-gray-800', 'text-white');
                const originalStyles = allBtn.getAttribute('data-original-styles');
                if (originalStyles) {
                    originalStyles.split(' ').forEach(cls => {
                        if (cls) allBtn.classList.add(cls);
                    });
                }
            }
        }

        if (selectedTags.includes(tag)) {
            // Remove tag if already selected
            selectedTags = selectedTags.filter(t => t !== tag);
            element.classList.remove('bg-gray-800', 'text-white');
            const originalStyles = element.getAttribute('data-original-styles');
            if (originalStyles) {
                originalStyles.split(' ').forEach(cls => {
                    if (cls) element.classList.add(cls);
                });
            }
        } else if (selectedTags.length < 3) {
            // Add tag if under the limit
            selectedTags.push(tag);
            element.classList.remove(...element.getAttribute('data-original-styles').split(' ').filter(Boolean));
            element.classList.add('bg-gray-800', 'text-white');
        }
    }

    selectedTagsInput.value = selectedTags.join(',');
}

// Function for submitting replies
function submitReply(event, actionUrl, postId) {
    event.preventDefault();

    const form = event.target;
    const submitButton = form.querySelector('button[type="submit"]');
    submitButton.disabled = true;

    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    fetch(actionUrl, {
        method: 'POST',
        body: new FormData(form),
        headers: {
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json'
        }
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Network response was not ok.');
        }
        return response.json();
    })
    .then(data => {
        if (data.message === 'Reply posted successfully') {
            updateReplyUI(data.reply, postId);
            form.reset();

            // Hide the reply form
            const replySection = form.closest('[x-data]');
            if (replySection && typeof Alpine !== 'undefined') {
                Alpine.raw(Alpine.$data(replySection)).showReply = false;
            }
        }
    })
    .catch(error => {
        console.error('There was a problem with the fetch operation:', error);
    })
    .finally(() => {
        submitButton.disabled = false;
    });
}

function updateReplyUI(reply, postId) {
    const replyContainer = document.createElement('div');
    replyContainer.className = 'mb-4';

    // Format the HTML for the new reply
    let replyHTML = `
        <p class="font-bold">
            <a href="/profile/${reply.user.id}" class="text-blue-500 hover:underline">
                ${reply.user.name}
            </a>
        </p>
        <p>${reply.content}</p>
    `;

    // Add metadata if available
    if (reply.metadata) {
        replyHTML += `
            <div class="mt-4 border rounded flex overflow-hidden">
                <a href="${reply.metadata.url}" target="_blank" class="flex w-full">
                    ${reply.metadata.image ? `<img src="${reply.metadata.image}" alt="${reply.metadata.title}" class="w-24 h-auto object-cover">` : ''}
                    <div class="p-4 flex-grow">
                        <h3 class="font-semibold text-lg">${reply.metadata.title}</h3>
                        <p>${reply.metadata.description}</p>
                    </div>
                </a>
            </div>
        `;
    }

    // Add timestamp
    replyHTML += `<p class="text-sm text-gray-500">just now</p>`;

    // Add delete button if allowed
    if (reply.can_delete) {
        replyHTML += `
            <form action="/replies/${reply.id}" method="POST" class="inline">
                <input type="hidden" name="_token" value="${document.querySelector('meta[name="csrf-token"]').getAttribute('content')}">
                <input type="hidden" name="_method" value="DELETE">
                <button type="submit" class="text-red-500">Delete</button>
            </form>
        `;
    }

    replyContainer.innerHTML = replyHTML;

    // Find and append to the correct post
    const postElement = document.getElementById(`post-${postId}`);
    if (postElement) {
        const repliesContainer = postElement.querySelector('.replies-container');
        if (repliesContainer) {
            repliesContainer.appendChild(replyContainer);

            // Scroll to show the new reply
            replyContainer.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        }
    }
}

// Initialize tags on page load
document.addEventListener('DOMContentLoaded', () => {
    const selectedTagsInput = document.getElementById('selected-tags');
    if (!selectedTagsInput) return;

    const selectedTags = selectedTagsInput.value.split(',').filter(Boolean);

    document.querySelectorAll('.tag-button').forEach(button => {
        const tag = button.getAttribute('data-tag');
        if (selectedTags.includes(tag)) {
            button.classList.remove(...button.getAttribute('data-original-styles').split(' ').filter(Boolean));
            button.classList.add('bg-gray-800', 'text-white');
        }
    });
});
</script>
