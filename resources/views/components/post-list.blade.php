@props(['posts', 'tags', 'selectedTags'])

@php
    $posts = collect($posts)->sortByDesc('created_at');
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
    <div class="flex flex-wrap gap-2 mb-4">
        @foreach($tags as $tag)
            <button type="button"
                    class="tag-button inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold
                    {{ in_array($tag, $selectedTags) ? 'bg-gray-800 text-white' : $tagColors[$tag] }}"
                    data-tag="{{ $tag }}"
                    data-original-styles="{{ $tagColors[$tag] }}">
                #{{ $tag }}
            </button>
        @endforeach
    </div>
    <input type="hidden" id="selected-tags" value="{{ implode(',', $selectedTags) }}">

    <div id="posts-container">
        @foreach($posts as $post)
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
                        <button type="submit" class="text-red-500">Deletar</button>
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
</div>

<script>
// Definindo essas variáveis no escopo global para garantir que elas estejam disponíveis
window.csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
// Store tag colors in a JavaScript variable
window.tagColors = @json($tagColors);

// Definindo toggleTag no escopo global (window)
window.toggleTag = function(element, tag) {
    const selectedTagsInput = document.getElementById('selected-tags');
    let selectedTags = selectedTagsInput.value ? selectedTagsInput.value.split(',') : [];

    if (tag === 'all') {
        if (selectedTags.includes('all')) {
            selectedTags = [];
            document.querySelectorAll('.tag-button').forEach(btn => {
                btn.classList.remove('bg-gray-800', 'text-white');
                const originalStyles = btn.getAttribute('data-original-styles');
                originalStyles.split(' ').forEach(cls => btn.classList.add(cls));
            });
        } else {
            selectedTags = ['all'];
            document.querySelectorAll('.tag-button').forEach(btn => {
                const btnTag = btn.getAttribute('data-tag');
                btn.classList.remove('bg-gray-800', 'text-white');
                const originalStyles = btn.getAttribute('data-original-styles');
                if (btnTag === 'all') {
                    btn.classList.add('bg-gray-800', 'text-white');
                } else {
                    originalStyles.split(' ').forEach(cls => btn.classList.add(cls));
                }
            });
        }
    } else {
        if (selectedTags.includes('all')) {
            selectedTags = selectedTags.filter(t => t !== 'all');
            const allBtn = document.querySelector('.tag-button[data-tag="all"]');
            if (allBtn) {
                allBtn.classList.remove('bg-gray-800', 'text-white');
                const originalStyles = allBtn.getAttribute('data-original-styles');
                originalStyles.split(' ').forEach(cls => allBtn.classList.add(cls));
            }
        }

        if (selectedTags.includes(tag)) {
            selectedTags = selectedTags.filter(t => t !== tag);
            element.classList.remove('bg-gray-800', 'text-white');
            const originalStyles = element.getAttribute('data-original-styles');
            originalStyles.split(' ').forEach(cls => element.classList.add(cls));
        } else if (selectedTags.length < 3) {
            selectedTags.push(tag);
            element.classList.remove(...element.getAttribute('data-original-styles').split(' ').filter(Boolean));
            element.classList.add('bg-gray-800', 'text-white');
        }
    }

    selectedTagsInput.value = selectedTags.join(',');
    fetchPosts(selectedTags);
};

function fetchPosts(tags) {
    fetch('{{ route('dashboard') }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': window.csrfToken,
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify({ tags: tags })
    })
    .then(response => response.json())
    .then(data => updatePosts(data.posts))
    .catch(error => console.error('Error fetching posts:', error));
}

function updatePosts(posts) {
    const container = document.getElementById('posts-container');
    container.innerHTML = '';

    posts.forEach(post => {
        const postElement = document.createElement('div');
        postElement.className = 'mb-8 p-4 border rounded';
        postElement.id = `post-${post.id}`;

        let tagsHtml = '';
        if (post.tag) {
            const tagColor = post.tag_colors && post.tag_colors[post.tag]
                ? post.tag_colors[post.tag]
                : window.tagColors[post.tag] || 'bg-gray-200 text-gray-700';
            tagsHtml += `<span class="inline-block ${tagColor} rounded-full px-3 py-1 text-xs font-semibold">#${post.tag}</span>`;
        }

        if (post.additional_tags && Array.isArray(post.additional_tags)) {
            post.additional_tags.forEach(tag => {
                const tagColor = post.tag_colors && post.tag_colors[tag]
                    ? post.tag_colors[tag]
                    : window.tagColors[tag] || 'bg-gray-200 text-gray-700';
                tagsHtml += `<span class="inline-block ${tagColor} rounded-full px-3 py-1 text-xs font-semibold">#${tag}</span>`;
            });
        }

        let metadataHtml = '';
        if (post.metadata && post.metadata.url) {
            metadataHtml = `
                <div class="mt-4 border rounded flex overflow-hidden">
                    <a href="${post.metadata.url}" target="_blank" class="flex w-full">
                        ${post.metadata.image ? `<img src="${post.metadata.image}" alt="${post.metadata.title}" class="w-24 h-auto object-cover">` : ''}
                        <div class="p-4 flex-grow">
                            <h3 class="font-semibold text-lg">${post.metadata.title}</h3>
                            <p>${post.metadata.description}</p>
                        </div>
                    </a>
                </div>
            `;
        }

        postElement.innerHTML = `
            <p class="font-bold">
                <a href="${post.user.profile_url}" class="text-blue-500 hover:underline">${post.user.name}</a>
            </p>
            <div class="flex flex-wrap gap-2 mb-2">${tagsHtml}</div>
            <p>${post.content}</p>
            ${metadataHtml}
            <p class="text-sm text-gray-500">${post.created_at_diff}</p>
            ${post.can_delete ? `
                <form action="/posts/${post.id}" method="POST" class="inline">
                    <input type="hidden" name="_token" value="${window.csrfToken}">
                    <input type="hidden" name="_method" value="DELETE">
                    <button type="submit" class="text-red-500">Delete</button>
                </form>
            ` : ''}
            <div x-data="{ showReply: false, replying: false }">
                <button @click="showReply = !showReply" class="text-blue-500">Reply</button>
                <div x-show="showReply" class="mt-2">
                    <form onsubmit="event.preventDefault(); window.submitReply(this, '${post.reply_url}', ${post.id});">
                        <input type="hidden" name="_token" value="${window.csrfToken}">
                        <textarea name="content" rows="2" class="w-full border rounded p-2" placeholder="Reply to this post"></textarea>
                        <button type="submit" class="mt-2 px-4 py-2 bg-blue-500 text-white rounded" :disabled="replying">Reply</button>
                    </form>
                </div>
            </div>
            <div class="replies-container ml-8 mt-4 border-l-2 border-gray-200 pl-4">
                ${post.replies.map(reply => `
                    <div class="mb-4">
                        <p class="font-bold">
                            <a href="${reply.user.profile_url}" class="text-blue-500 hover:underline">${reply.user.name}</a>
                        </p>
                        <p>${reply.content}</p>
                        ${reply.metadata && reply.metadata.url ? `
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
                        <p class="text-sm text-gray-500">${reply.created_at_diff}</p>
                        ${reply.can_delete ? `
                            <form action="/replies/${reply.id}" method="POST" class="inline">
                                <input type="hidden" name="_token" value="${window.csrfToken}">
                                <input type="hidden" name="_method" value="DELETE">
                                <button type="submit" class="text-red-500">Delete</button>
                            </form>
                        ` : ''}
                    </div>
                `).join('')}
            </div>
        `;

        container.appendChild(postElement);
    });
}

// Definindo submitReply no escopo global
window.submitReply = function(form, actionUrl, postId) {
    const submitButton = form.querySelector('button[type="submit"]');
    submitButton.disabled = true;

    fetch(actionUrl, {
        method: 'POST',
        body: new FormData(form),
        headers: {
            'X-CSRF-TOKEN': window.csrfToken,
            'Accept': 'application/json'
        }
    })
    .then(response => {
        if (!response.ok) throw new Error('Network response was not ok.');
        return response.json();
    })
    .then(data => {
        if (data.message === 'Reply posted successfully') {
            updateReplyUI(data.reply, postId);
            form.reset();
            const replySection = form.closest('[x-data]');
            if (replySection && typeof Alpine !== 'undefined') {
                Alpine.raw(Alpine.$data(replySection)).showReply = false;
            }
        }
    })
    .catch(error => console.error('Error:', error))
    .finally(() => submitButton.disabled = false);
};

// Function to update the UI with a new reply
function updateReplyUI(reply, postId) {
    const replyContainer = document.createElement('div');
    replyContainer.className = 'mb-4';

    let replyHTML = `
        <p class="font-bold">
            <a href="/profile/${reply.user.id}" class="text-blue-500 hover:underline">${reply.user.name}</a>
        </p>
        <p>${reply.content}</p>
    `;

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

    replyHTML += `<p class="text-sm text-gray-500">just now</p>`;
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
    const postElement = document.getElementById(`post-${postId}`);
    if (postElement) {
        const repliesContainer = postElement.querySelector('.replies-container');
        if (repliesContainer) {
            repliesContainer.appendChild(replyContainer);
            replyContainer.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        }
    }
}

// Adicionar eventos de clique aos botões tag após o carregamento do DOM
document.addEventListener('DOMContentLoaded', () => {
    // Adicionar o onclick aos botões de tag
    document.querySelectorAll('.tag-button').forEach(button => {
        const tag = button.getAttribute('data-tag');
        button.addEventListener('click', function() {
            window.toggleTag(this, tag);
        });
    });

    // Aplicar estilo aos botões de tag selecionados
    const selectedTagsInput = document.getElementById('selected-tags');
    if (selectedTagsInput && selectedTagsInput.value) {
        const selectedTags = selectedTagsInput.value.split(',').filter(Boolean);
        document.querySelectorAll('.tag-button').forEach(button => {
            const tag = button.getAttribute('data-tag');
            if (selectedTags.includes(tag)) {
                button.classList.remove(...button.getAttribute('data-original-styles').split(' ').filter(Boolean));
                button.classList.add('bg-gray-800', 'text-white');
            }
        });
    }
});
</script>
