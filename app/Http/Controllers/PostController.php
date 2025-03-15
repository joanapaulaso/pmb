<?php

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
                    'is_lab_publication' => $post->is_lab_publication ?? false, // Adicionar nova propriedade
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

    /**
     * Store a newly created post
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'content' => 'required|max:5000',
            'tag' => 'required|in:geral,pergunta,oportunidade,divulgação,ideia,colaboração,notícia,publicação',
            'additional_tags' => 'sometimes|array|max:2',
            'additional_tags.*' => 'in:geral,pergunta,oportunidade,divulgação,ideia,colaboração,notícia,publicação',
            'is_lab_publication' => 'required|boolean' // Validação para a nova coluna
        ]);

        // Sanitize HTML content
        $content = $this->sanitizeHtml($validated['content']);

        $metadata = [];

        try {
            // Extrair URLs do conteúdo HTML, excluindo localhost
            $shouldExtractLink = false;
            $url = null;

            // Procurar URLs no conteúdo que não sejam localhost
            if (preg_match('/\bhttps?:\/\/(?!localhost)\S+/i', $content, $match)) {
                $url = $match[0];
                $shouldExtractLink = true;
            } elseif (preg_match('/href=["\']([^"\']+)["\']/i', $content, $match)) {
                $possibleUrl = $match[1];
                if (strpos($possibleUrl, 'localhost') === false) {
                    $url = $possibleUrl;
                    $shouldExtractLink = true;
                }
            }

            // Só extrair metadados se for uma URL válida e não for localhost
            if ($shouldExtractLink && $url) {
                try {
                    $metadata = $this->linkPreviewService->getPreview($url);
                    \Log::info('Preview obtido com sucesso', ['url' => $url]);
                } catch (\Exception $e) {
                    \Log::error('Erro ao obter preview do link', [
                        'url' => $url,
                        'error' => $e->getMessage()
                    ]);
                    $metadata = [
                        'url' => $url,
                        'title' => parse_url($url, PHP_URL_HOST) ?: 'Link',
                        'description' => 'Não foi possível carregar informações deste link'
                    ];
                }
            }
        } catch (\Exception $e) {
            \Log::error('Erro ao processar URL no conteúdo', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            // Continuar sem metadata em caso de erro
        }

        try {
            // Create the post with additional tags and is_lab_publication
            $post = $request->user()->posts()->create([
                'content' => $content,
                'tag' => $validated['tag'],
                'additional_tags' => $request->input('additional_tags', []),
                'metadata' => $metadata,
                'is_lab_publication' => $validated['is_lab_publication'] // Salvar a nova coluna
            ]);

            return redirect()->route('dashboard');
        } catch (\Exception $e) {
            \Log::error('Erro ao salvar post', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->route('dashboard')
                ->with('error', 'Ocorreu um erro ao salvar seu post. Por favor, tente novamente.');
        }
    }

    // Handle replying to a post
    public function reply(Request $request, Post $post)
    {
        \Log::info('Reply attempt', ['user' => $request->user()->id, 'post' => $post->id, 'content' => $request->content]);

        $validated = $request->validate([
            'content' => 'required|max:5000',
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
                $url = $match[1];
            } else {
                $url = $match[0];
            }

            try {
                $metadata = $this->linkPreviewService->getPreview($url);

                if (!isset($metadata['url'])) $metadata['url'] = $url;
                if (!isset($metadata['title'])) $metadata['title'] = 'Link';
                if (!isset($metadata['description'])) $metadata['description'] = '';
            } catch (\Exception $e) {
                \Log::warning('Erro ao obter preview do link', [
                    'url' => $url,
                    'error' => $e->getMessage()
                ]);
                $metadata = [
                    'url' => $url,
                    'title' => 'Link',
                    'description' => ''
                ];
            }
        }

        if (!empty($metadata) && isset($metadata['url']) && isset($metadata['title'])) {
            $reply->metadata = $metadata;
        } else {
            $reply->metadata = null;
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
     * Limpeza adicional do HTML para remover pontos após imagens e prevenir links indesejados
     */
    protected function cleanupHtml($html)
    {
        if (empty($html)) {
            return '';
        }

        $html = preg_replace('/<img([^>]*)>\s*\.\s*/', '<img$1>', $html);
        $html = preg_replace('/<a[^>]*localhost[^>]*>([^<]*)<\/a>/', '$1', $html);
        $html = preg_replace('/<([a-z]+)[^>]*>\s*\.\s*<\/\1>/', '.', $html);

        $pattern = '/<([a-z]+)[^>]*>\s*(<img[^>]*>)\s*<\/\1>/i';
        $replacement = '$2';
        $oldHtml = '';
        while ($oldHtml !== $html) {
            $oldHtml = $html;
            $html = preg_replace($pattern, $replacement, $html);
        }

        return $html;
    }

    /**
     * Sanitize HTML content for security while preserving Quill formatting
     */
    protected function sanitizeHtml($html)
    {
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
            'sub'
        ];

        if (class_exists('\HTMLPurifier')) {
            try {
                $config = \HTMLPurifier_Config::createDefault();
                $config->set('Core.Encoding', 'UTF-8');
                $config->set('HTML.Doctype', 'HTML 4.01 Transitional');
                $config->set('Cache.SerializerPath', storage_path('app/purifier'));

                if (!file_exists(storage_path('app/purifier'))) {
                    mkdir(storage_path('app/purifier'), 0755, true);
                }

                $config->set('CSS.AllowedProperties', 'font,font-size,font-weight,font-style,font-family,text-decoration,padding-left,color,background-color,text-align');
                $config->set('CSS.AllowTricky', true);
                $config->set('HTML.Trusted', true);
                $config->set('Core.EscapeInvalidTags', true);
                $config->set('HTML.SafeIframe', true);
                $config->set('URI.SafeIframeRegexp', '%^(https?:)?//(www\.youtube(?:-nocookie)?\.com/embed/|player\.vimeo\.com/video/)%');

                $def = $config->getHTMLDefinition(true);
                $def->addAttribute('img', 'src', 'URI');
                $def->addAttribute('img', 'alt', 'Text');
                $def->addAttribute('img', 'class', 'CDATA');
                $def->addAttribute('img', 'width', 'Number');
                $def->addAttribute('img', 'height', 'Number');
                $def->addAttribute('a', 'href', 'URI');
                $def->addAttribute('a', 'target', 'Enum#_blank,_self,_target,_top');
                $def->addAttribute('a', 'rel', 'CDATA');

                foreach ($allowedTags as $tag) {
                    $def->addAttribute($tag, 'class', 'CDATA');
                    $def->addAttribute($tag, 'style', 'CDATA');
                }

                $purifier = new \HTMLPurifier($config);
                $clean = $purifier->purify($html);
                return $this->cleanupHtml($clean);
            } catch (\Exception $e) {
                \Log::error('Erro no HTMLPurifier: ' . $e->getMessage());
                $stripped = strip_tags($html, '<' . implode('><', $allowedTags) . '>');
                return $this->cleanupHtml($stripped);
            }
        }

        $stripped = strip_tags($html, '<' . implode('><', $allowedTags) . '>');
        return $this->cleanupHtml($stripped);
    }

    public function destroy(Post $post)
    {
        if (Gate::denies('delete', $post)) {
            abort(403);
        }

        $post->delete();

        return redirect()->back()->with('success', 'Post deleted successfully');
    }

    public function destroyReply(Post $reply)
    {
        $this->authorize('delete', $reply);
        $reply->delete();
        return redirect()->back();
    }
}
