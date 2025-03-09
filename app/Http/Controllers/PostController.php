<?php

// In your PostController
namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use App\Http\Controllers\Controller;
use App\Services\LinkPreviewService;
use Illuminate\Support\Str;

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
            ->latest();
        \Log::info('Query built');
        $selectedTags = $request->input('tags', []);

        if ($request->isMethod('post') && $request->expectsJson()) {
            if (!is_array($selectedTags)) {
                $selectedTags = array_filter(explode(',', $selectedTags));
            }
            $selectedTags = array_slice($selectedTags, 0, 3);

            if (!empty($selectedTags) && !in_array('all', $selectedTags)) {
                $query->where(function ($q) use ($selectedTags) {
                    foreach ($selectedTags as $tag) {
                        $q->where('tag', $tag)
                            ->orWhereJsonContains('additional_tags', $tag);
                    }
                });
            }

            $postsQuery = clone $query;
            $posts = $postsQuery->get()->map(function ($post) use ($tagColors) {
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
                    'tag_colors' => $tagColors, // Pass all tag colors
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

            return response()->json(['posts' => $posts, 'pagination' => $query->paginate(10)->links()]);
        }

        if (!is_array($selectedTags)) {
            $selectedTags = explode(',', $selectedTags);
        }
        $selectedTags = array_slice($selectedTags, 0, 3);

        if (!empty($selectedTags) && !in_array('all', $selectedTags)) {
            $query->where(function ($q) use ($selectedTags) {
                foreach ($selectedTags as $tag) {
                    $q->where('tag', $tag)
                        ->orWhereJsonContains('additional_tags', $tag);
                }
            });
        }

        $posts = $query->paginate(10);
        $tags = array_keys($tagColors);

        return view('dashboard', compact('posts', 'tags', 'selectedTags', 'tagColors'));
    }


    public function store(Request $request)
    {
        $validated = $request->validate([
            'content' => 'required|max:5000', // Aumentado o tamanho máximo para conteúdo HTML
            'tag' => 'required|in:general,question,job,promotion,idea,collaboration,news,paper',
            'additional_tags' => 'sometimes|array|max:2',
            'additional_tags.*' => 'in:general,question,job,promotion,idea,collaboration,news,paper'
        ]);

        // Sanitize HTML content
        $content = $this->sanitizeHtml($validated['content']);

        $metadata = [];

        // Extrair URLs do conteúdo HTML
        if (
            preg_match('/\bhttps?:\/\/\S+/i', $content, $match) ||
            preg_match('/href=["\']([^"\']+)["\']/i', $content, $match)
        ) {
            if (isset($match[1])) {
                $url = $match[1]; // Pega a URL do atributo href
            } else {
                $url = $match[0]; // Pega a URL encontrada diretamente no texto
            }
            $metadata = $this->linkPreviewService->getPreview($url);
        }

        // Create the post with additional tags
        $post = $request->user()->posts()->create([
            'content' => $content,
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
            'content' => 'required|max:5000', // Aumentado para suportar HTML
        ]);

        // Sanitize HTML content for reply
        $content = $this->sanitizeHtml($validated['content']);

        // Check if a reply with the same content was recently created
        $existingReply = Post::where('parent_id', $post->id)
            ->where('user_id', $request->user()->id)
            ->where('content', $content)
            ->where('created_at', '>', now()->subSeconds(5))
            ->first();

        if ($existingReply) {
            \Log::info('Duplicate reply detected', ['existing_reply' => $existingReply->id]);
            return response()->json([
                'message' => 'Reply already posted',
                'reply' => $existingReply->load('user')
            ]);
        }

        $reply = new Post();
        $reply->content = $content;
        $reply->user_id = $request->user()->id;
        $reply->parent_id = $post->id;

        // Extract metadata from reply content if it contains a URL
        $metadata = [];
        if (
            preg_match('/\bhttps?:\/\/\S+/i', $content, $match) ||
            preg_match('/href=["\']([^"\']+)["\']/i', $content, $match)
        ) {
            if (isset($match[1])) {
                $url = $match[1]; // URL do atributo href
            } else {
                $url = $match[0]; // URL direta
            }

            try {
                $metadata = $this->linkPreviewService->getPreview($url);

                // Garantir que temos todos os campos necessários para evitar "undefined"
                if (!isset($metadata['url'])) $metadata['url'] = $url;
                if (!isset($metadata['title'])) $metadata['title'] = 'Link';
                if (!isset($metadata['description'])) $metadata['description'] = '';
            } catch (\Exception $e) {
                \Log::warning('Erro ao obter preview do link', [
                    'url' => $url,
                    'error' => $e->getMessage()
                ]);
                // Criar metadata básico se o serviço falhar
                $metadata = [
                    'url' => $url,
                    'title' => 'Link',
                    'description' => ''
                ];
            }
        }

        // Só salvar metadata se tiver os campos essenciais
        if (!empty($metadata) && isset($metadata['url']) && isset($metadata['title'])) {
            $reply->metadata = $metadata;
        } else {
            $reply->metadata = null; // Garantir que seja null em vez de array vazio
        }

        $reply->save();

        \Log::info('New reply created', ['reply' => $reply->id]);

        return response()->json([
            'message' => 'Reply posted successfully',
            'reply' => [
                'id' => $reply->id,
                'user' => [
                    'name' => $reply->user->name,
                    'id' => $reply->user->id,
                    'profile_url' => route('public.profile', $reply->user)
                ],
                'content' => $reply->content,
                'metadata' => $reply->metadata,
                'created_at' => $reply->created_at->toDateTimeString(),
                'can_delete' => $request->user()->can('delete', $reply),
            ]
        ]);
    }

    /**
     * Sanitize HTML content for security while preserving Quill formatting
     */
    protected function sanitizeHtml($html)
    {
        // Lista de tags permitidas
        $allowedTags = [
            'p',
            'br',
            'b',
            'i',
            'u',
            's',
            'strong',
            'em',
            'ul',
            'ol',
            'li',
            'blockquote',
            'h1',
            'h2',
            'h3',
            'h4',
            'h5',
            'h6',
            'a',
            'img',
            'pre',
            'code',
            'span',
            'div',
            'sup',
            'sub'  // Importante para preservar formatação do Quill
        ];

        // Lista de atributos permitidos por tag
        $allowedAttrs = [
            'a' => ['href', 'target', 'rel'],
            'img' => ['src', 'alt', 'width', 'height'],
            'span' => ['class', 'style'],  // Importante para formatação do Quill
            'p' => ['class', 'style'],     // Importante para alinhamento
            'h1' => ['class', 'style'],
            'h2' => ['class', 'style'],
            'h3' => ['class', 'style'],
            'h4' => ['class', 'style'],
            'h5' => ['class', 'style'],
            'h6' => ['class', 'style'],
            'ul' => ['class', 'style'],
            'ol' => ['class', 'style'],
            'li' => ['class', 'style'],
            'blockquote' => ['class', 'style'],
            '*' => ['class', 'style']      // Atributos globais permitidos em todas as tags
        ];

        // Configurar HTMLPurifier (se estiver disponível)
        if (class_exists('\HTMLPurifier')) {
            $config = \HTMLPurifier_Config::createDefault();

            // Permitir todas as classes CSS começando com "ql-"
            $config->set('CSS.AllowedProperties', 'text-align,font-weight,font-style,text-decoration,color,background-color,font-family,font-size');
            $config->set('CSS.AllowTricky', true);
            $config->set('HTML.SafeIframe', true);

            // Configurar tags permitidas
            $config->set('HTML.Allowed', implode(',', array_map(function ($tag) {
                return $tag . '[*]'; // Permitir todos atributos inicialmente, refinamos depois
            }, $allowedTags)));

            // Configurar atributos permitidos
            foreach ($allowedAttrs as $tag => $attrs) {
                if ($tag === '*') {
                    foreach ($attrs as $attr) {
                        $config->set('HTML.AllowedAttributes', '*@' . $attr);
                    }
                } else {
                    foreach ($attrs as $attr) {
                        $config->set('HTML.AllowedAttributes', $tag . '@' . $attr);
                    }
                }
            }

            // Permitir classes CSS do Quill
            $config->set('Attr.AllowedClasses', [
                'ql-size-small',
                'ql-size-large',
                'ql-size-huge',
                'ql-font-serif',
                'ql-font-monospace',
                'ql-align-center',
                'ql-align-right',
                'ql-align-justify',
                'ql-bg-black',
                'ql-bg-red',
                'ql-bg-orange',
                'ql-bg-yellow',
                'ql-bg-green',
                'ql-bg-blue',
                'ql-bg-purple',
                'ql-color-black',
                'ql-color-red',
                'ql-color-orange',
                'ql-color-yellow',
                'ql-color-green',
                'ql-color-blue',
                'ql-color-purple',
                'ql-indent-1',
                'ql-indent-2',
                'ql-indent-3',
                'ql-indent-4',
                'ql-indent-5',
                'ql-indent-6',
                'ql-indent-7',
                'ql-indent-8',
                'ql-indent-9',
                'ql-list',
                'ql-code-block',
                'ql-video',
                'ql-formula',
                'ql-image'
            ]);

            $purifier = new \HTMLPurifier($config);
            return $purifier->purify($html);
        }

        // Fallback caso HTMLPurifier não esteja disponível
        // Este método é menos seguro e não preserva certas formatações
        if (!function_exists('strip_tags_content')) {
            function strip_tags_content($text, $tags = '', $invert = FALSE)
            {
                preg_match_all('/<(.+?)[\s]*\/?[\s]*>/si', trim($tags), $tags);
                $tags = array_unique($tags[1]);

                if (is_array($tags) and count($tags) > 0) {
                    if ($invert == FALSE) {
                        return preg_replace('@<(?!(?:' . implode('|', $tags) . ')\b)(\w+)\b.*?>.*?</\1>@si', '', $text);
                    } else {
                        return preg_replace('@<(' . implode('|', $tags) . ')\b.*?>.*?</\1>@si', '', $text);
                    }
                } elseif ($invert == FALSE) {
                    return preg_replace('@<(\w+)\b.*?>.*?</\1>@si', '', $text);
                }
                return $text;
            }
        }

        return strip_tags($html, '<' . implode('><', $allowedTags) . '>');
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
