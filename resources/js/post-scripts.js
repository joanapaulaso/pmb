// post-scripts.js
const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

export function toggleTag(element, tag, tagColors) {
    const selectedTagsInput = document.getElementById('selected-tags');
    let selectedTags = selectedTagsInput.value ? selectedTagsInput.value.split(',') : [];

    if (tag === 'all') {
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
                const originalStyles = btn.getAttribute('data-original-styles');
                if (originalStyles) {
                    originalStyles.split(' ').forEach(cls => {
                        if (cls) btn.classList.remove(cls);
                    });
                }
                if (btnTag === 'all') {
                    btn.classList.add('bg-gray-800', 'text-white');
                } else {
                    if (originalStyles) {
                        originalStyles.split(' ').forEach(cls => {
                            if (cls) btn.classList.add(cls);
                        });
                    }
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
                if (originalStyles) {
                    originalStyles.split(' ').forEach(cls => {
                        if (cls) allBtn.classList.add(cls);
                    });
                }
            }
        }

        if (selectedTags.includes(tag)) {
            selectedTags = selectedTags.filter(t => t !== tag);
            element.classList.remove('bg-gray-800', 'text-white');
            const originalStyles = element.getAttribute('data-original-styles');
            if (originalStyles) {
                originalStyles.split(' ').forEach(cls => {
                    if (cls) element.classList.add(cls);
                });
            }
        } else if (selectedTags.length < 3) {
            selectedTags.push(tag);
            const originalStyles = element.getAttribute('data-original-styles');
            if (originalStyles) {
                originalStyles.split(' ').forEach(cls => {
                    if (cls) element.classList.remove(cls);
                });
            }
            element.classList.add('bg-gray-800', 'text-white');
        }
    }

    selectedTagsInput.value = selectedTags.join(',');
    fetchPosts(selectedTags);
}

export function fetchPosts(tags, dashboardRoute) {
    fetch(dashboardRoute, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': csrfToken,
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

export function updatePosts(posts) {
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
                    <input type="hidden" name="_token" value="${csrfToken}">
                    <input type="hidden" name="_method" value="DELETE">
                    <button type="submit" class="text-red-500">Delete</button>
                </form>
            ` : ''}
            <div x-data="{ showReply: false, replying: false }">
                <button @click="showReply = !showReply" class="text-blue-500">Reply</button>
                <div x-show="showReply" class="mt-2">
                    <form onsubmit="event.preventDefault(); submitReply(this, '${post.reply_url}', ${post.id});">
                        <input type="hidden" name="_token" value="${csrfToken}">
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
                                <input type="hidden" name="_token" value="${csrfToken}">
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

export function submitReply(form, actionUrl, postId) {
    const submitButton = form.querySelector('button[type="submit"]');
    submitButton.disabled = true;

    fetch(actionUrl, {
        method: 'POST',
        body: new FormData(form),
        headers: {
            'X-CSRF-TOKEN': csrfToken,
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
}

export function updateReplyUI(reply, postId) {
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
                <input type="hidden" name="_token" value="${csrfToken}">
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

document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.tag-button').forEach(button => {
        const tag = button.getAttribute('data-tag');
        button.addEventListener('click', function() {
            toggleTag(this, tag, window.tagColors);
        });
    });

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
