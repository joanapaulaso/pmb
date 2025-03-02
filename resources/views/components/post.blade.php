<div class="mb-8 border-b pb-4" x-data="{ openReplyForm: false }">
    <div class="bg-white p-4 rounded-lg shadow-md">
        <p class="font-bold">{{ $post->user->name }}</p>
        <p>{{ $post->content }}</p>
        <p class="text-gray-500 text-sm">{{ $post->created_at->format('M d, Y H:i') }}</p>
        <button x-on:click="openReplyForm = !openReplyForm" class="text-blue-500 mt-2">Reply</button>
    </div>

    <div id="reply-form-{{ $post->id }}" class="mt-2" x-show="openReplyForm">
        <form action="{{ route('posts.store') }}" method="POST">
            @csrf
            <input type="hidden" name="parent_id" value="{{ $post->id }}">
            <textarea name="content" rows="2" class="w-full p-2 rounded-lg border" placeholder="Reply to this post"></textarea>
            <button type="submit" class="mt-2 bg-blue-500 text-white px-2 py-1 rounded">Submit Reply</button>
        </form>
    </div>

    <div id="replies-{{ $post->id }}" class="mt-4 ml-8">
        @foreach($posts as $post)
            <div class="post">
                <!-- Display post content here -->
                @if($post->replies)
                    <div class="replies">
                        @include('components.post-form', ['post' => $post])
                    </div>
                @endif
            </div>
        @endforeach
    </div>
</div>
