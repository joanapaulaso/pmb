<?php

// In your PostController
namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use App\Http\Controllers\Controller;
use App\Services\LinkPreviewService;

class PostController extends Controller
{
    protected $linkPreviewService;

    public function __construct(LinkPreviewService $linkPreviewService)
    {
        $this->linkPreviewService = $linkPreviewService;
    }

    public function index(Request $request)
    {
        \Log::info('Starting index method');
        $tagColors = config('tags.colors');
        $query = Post::with(['user', 'replies.user'])
            ->whereNull('parent_id')
            ->latest()
            ->paginate(10);
        \Log::info('Query built');
        $selectedTags = $request->input('tags', []);

        if ($request->isMethod('post') && $request->expectsJson()) {
            if (!is_array($selectedTags)) {
                $selectedTags = array_filter(explode(',', $selectedTags));
            }
            $selectedTags = array_slice($selectedTags, 0, 3);

            if (!empty($selectedTags) && !in_array('all', $selectedTags)) {
                $query->where(function ($q) use ($selectedTags) {
                    $q->whereIn('tag', $selectedTags)
                        ->orWhereJsonContains('additional_tags', $selectedTags);
                });
            }

            $posts = $query->get()->map(function ($post) use ($tagColors) {
                return [
                    'id' => $post->id,
                    'user' => [
                        'id' => $post->user->id,
                        'name' => $post->user->name,
                        'profile_url' => route('public.profile', $post->user),
                    ],
                    'content' => $post->content,
                    'tag' => $post->tag,
                    'tag_color' => $tagColors[$post->tag] ?? 'bg-gray-200 text-gray-700',
                    'additional_tags' => $post->additional_tags ?? [],
                    'tag_colors' => $tagColors,
                    'metadata' => $post->metadata ?? [],
                    'created_at_diff' => $post->created_at->diffForHumans(),
                    'can_delete' => auth()->check() && auth()->user()->can('delete', $post),
                    'reply_url' => route('posts.reply', $post),
                    'replies' => $post->replies->map(function ($reply) use ($tagColors) {
                        return [
                            'id' => $reply->id,
                            'user' => [
                                'id' => $reply->user->id,
                                'name' => $reply->user->name,
                                'profile_url' => route('public.profile', $reply->user),
                            ],
                            'content' => $reply->content,
                            'metadata' => $reply->metadata ?? [],
                            'created_at_diff' => $reply->created_at->diffForHumans(),
                            'can_delete' => auth()->check() && auth()->user()->can('delete', $reply),
                        ];
                    })->toArray(),
                ];
            });

            return response()->json(['posts' => $posts, 'pagination' => $query->links()]);

            \Log::info('Query executed', ['post_count' => $posts->count()]);
        }

        if (!is_array($selectedTags)) {
            $selectedTags = explode(',', $selectedTags);
        }
        $selectedTags = array_slice($selectedTags, 0, 3);

        if (!empty($selectedTags) && !in_array('all', $selectedTags)) {
            $query->where(function ($q) use ($selectedTags) {
                $q->whereIn('tag', $selectedTags)
                    ->orWhereJsonContains('additional_tags', $selectedTags);
            });
        }

        $posts = $query->get();
        $tags = array_keys($tagColors);

        return view('dashboard', compact('posts', 'tags', 'selectedTags'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'content' => 'required|max:280',
            'tag' => 'required|in:general,question,job,promotion,idea,collaboration,news,paper',
            'additional_tags' => 'sometimes|array|max:2',
            'additional_tags.*' => 'in:general,question,job,promotion,idea,collaboration,news,paper'
        ]);

        $metadata = [];
        if (preg_match('/\bhttps?:\/\/\S+/i', $request->content, $match)) {
            $metadata = $this->linkPreviewService->getPreview($match[0]);
        }

        // Create the post with additional tags
        $post = $request->user()->posts()->create([
            'content' => $validated['content'],
            'tag' => $validated['tag'],
            'additional_tags' => $request->input('additional_tags', []),
            'metadata' => $metadata
        ]);

        return redirect()->route('dashboard');
    }

    // Handle replying to a post
    public function reply(Request $request, Post $post)
    {
        \Log::info('Reply attempt', ['user' => $request->user()->id, 'post' => $post->id, 'content' => $request->content]);

        $validated = $request->validate([
            'content' => 'required|max:280',
        ]);

        // Check if a reply with the same content was recently created
        $existingReply = Post::where('parent_id', $post->id)
            ->where('user_id', $request->user()->id)
            ->where('content', $validated['content'])
            ->where('created_at', '>', now()->subSeconds(5))
            ->first();

        if ($existingReply) {
            \Log::info('Duplicate reply detected', ['existing_reply' => $existingReply->id]);
            return response()->json([
                'message' => 'Reply already posted',
                'reply' => $existingReply->load('user')
            ]);
        }

        $reply = new Post($validated);
        $reply->user_id = $request->user()->id;
        $reply->parent_id = $post->id;
        $reply->save();

        \Log::info('New reply created', ['reply' => $reply->id]);

        return response()->json([
            'message' => 'Reply posted successfully',
            'reply' => [
                'id' => $reply->id,
                'user' => ['name' => $reply->user->name],
                'content' => $reply->content,
                'metadata' => $reply->metadata,
                'created_at' => $reply->created_at->toDateTimeString(),
                'can_delete' => $request->user()->can('delete', $reply),
            ]
        ]);
    }

    public function destroy(Post $post)
    {
        if (Gate::denies('delete', $post)) {
            abort(403);
        }

        $post->delete();

        return redirect()->back()->with('success', 'Post deleted successfully');
    }
    // Handle reply deletion
    public function destroyReply(Post $reply)
    {
        $this->authorize('delete', $reply);
        $reply->delete();
        return redirect()->back();
    }
}
