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
        $query = Post::with(['user', 'replies.user'])
            ->whereNull('parent_id')
            ->latest();

        $selectedTags = $request->input('tags', []);

        if (!is_array($selectedTags)) {
            $selectedTags = explode(',', $selectedTags);
        }

        // Limit tag selection to 3
        $selectedTags = array_slice($selectedTags, 0, 3);

        if (!empty($selectedTags) && !in_array('all', $selectedTags)) {
            $query->where(function ($q) use ($selectedTags) {
                $q->whereIn('tag', $selectedTags)
                    ->orWhereJsonContains('additional_tags', $selectedTags);
            });
        }

        $posts = $query->get();
        $tags = ['all', 'general', 'question', 'job', 'promotion', 'idea', 'collaboration', 'news', 'paper'];

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
