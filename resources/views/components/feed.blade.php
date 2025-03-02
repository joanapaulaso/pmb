@props(['posts'])

<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
            <form action="{{ route('posts.store') }}" method="POST">
                @csrf
                <div class="mb-4">
                    <label for="content" class="sr-only">Content</label>
                    <textarea name="content" id="content" cols="30" rows="4" class="bg-gray-100 border-2 w-full p-4 rounded-lg @error('content') border-red-500 @enderror" placeholder="What's on your mind?"></textarea>

                    @error('content')
                    <div class="text-red-500 mt-2 text-sm">{{ $message }}</div>
                    @enderror
                </div>
                <div>
                    <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded font-medium w-full">Post</button>
                </div>
            </form>

            <div class="mt-6">
                @forelse ($posts->sortByDesc('created_at') as $post)
                    @include('components.post', ['post' => $post])
                @empty
                    <p>No posts yet.</p>
                @endforelse
            </div>
        </div>
    </div>
</div>
