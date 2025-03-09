<?php

// In your PostPortalController
namespace App\Http\Controllers;

use App\Models\PostPortal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use App\Http\Controllers\Controller;
use App\Services\LinkPreviewService;
use Illuminate\Support\Str;

class PostPortalController extends Controller
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
        $query = PostPortal::with(['user', 'replies.user'])
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
                    'reply_url' => route('posts-portal.reply', $post),
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

        return view('portal', compact('posts', 'tags', 'selectedTags', 'tagColors'));
    }

    /**
     * Store a newly created post
     */
    public function store(Request $request)
    {
        // Add detailed logging
        \Log::info('Post submission attempt', [
            'user_id' => $request->user()->id,
            'content_length' => strlen($request->input('content')),
            'tag' => $request->input('tag'),
            'additional_tags' => $request->input('additional_tags')
        ]);

        try {
            $validated = $request->validate([
                'content' => 'required|max:10000', // Increased max size to handle HTML with images
                'tag' => 'required|in:general,question,job,promotion,idea,collaboration,news,paper',
                'additional_tags' => 'sometimes|array|max:2',
                'additional_tags.*' => 'in:general,question,job,promotion,idea,collaboration,news,paper'
            ]);

            // Log validation success
            \Log::info('Post validation passed');

            // Sanitize HTML content
            $content = $this->sanitizeHtml($validated['content']);

            // Log content after sanitization
            \Log::info('Content sanitized', [
                'content_length' => strlen($content),
                'contains_image' => strpos($content, '<img') !== false
            ]);

            $metadata = [];

            try {
                // Extract URLs from HTML content, excluding localhost and image URLs
                $shouldExtractLink = false;
                $url = null;

                // Primeiro verifique se há URLs de texto no conteúdo que não são imagens
                if (preg_match('/\bhttps?:\/\/(?!localhost)[^"\'<>]+(?!\.(?:jpg|jpeg|png|gif|webp))/i', $content, $match)) {
                    $url = $match[0]; // URL encontrada no texto que não é imagem
                    $shouldExtractLink = true;
                }
                // Depois verifique links href (excluindo imagens)
                elseif (preg_match('/href=["\']([^"\']+)(?!\.(?:jpg|jpeg|png|gif|webp))["\'](?!.*<img)/i', $content, $match)) {
                    $possibleUrl = $match[1];
                    // Verificar se não é localhost
                    if (
                        strpos($possibleUrl, 'localhost') === false &&
                        strpos($possibleUrl, '.jpg') === false &&
                        strpos($possibleUrl, '.jpeg') === false &&
                        strpos($possibleUrl, '.png') === false &&
                        strpos($possibleUrl, '.gif') === false
                    ) {
                        $url = $possibleUrl;
                        $shouldExtractLink = true;
                    }
                }

                // Apenas extrair metadados se for uma URL válida que não seja imagem
                if ($shouldExtractLink && $url) {
                    // Use try-catch specific to the link preview service
                    try {
                        $metadata = $this->linkPreviewService->getPreview($url);
                        \Log::info('Preview obtained successfully', ['url' => $url]);
                    } catch (\Exception $e) {
                        \Log::error('Error getting link preview', [
                            'url' => $url,
                            'error' => $e->getMessage()
                        ]);

                        // Use a default preview in case of error
                        $metadata = [
                            'url' => $url,
                            'title' => parse_url($url, PHP_URL_HOST) ?: 'Link',
                            'description' => 'Could not load information for this link'
                        ];
                    }
                }
            } catch (\Exception $e) {
                \Log::error('Error processing URL in content', [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
                // Continue without metadata in case of error
            }

            try {
                // Create the post with additional tags
                $post = $request->user()->portalPosts()->create([
                    'content' => $content,
                    'tag' => $validated['tag'],
                    'additional_tags' => $request->input('additional_tags', []),
                    'metadata' => $metadata
                ]);

                \Log::info('Post created successfully', ['post_id' => $post->id]);

                return redirect()->route('portal')->with('success', 'Post created successfully');
            } catch (\Exception $e) {
                \Log::error('Error saving post', [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                    'sql' => isset($e->getSql) ? $e->getSql() : 'N/A'
                ]);

                return redirect()->route('portal')
                    ->with('error', 'An error occurred while saving your post. Please try again.');
            }
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Post validation failed', [
                'errors' => $e->errors()
            ]);

            return redirect()->route('portal')
                ->withErrors($e->errors())
                ->withInput();
        } catch (\Exception $e) {
            \Log::error('Unexpected error in post creation', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->route('portal')
                ->with('error', 'An unexpected error occurred. Please try again.');
        }
    }

    // Handle replying to a post
    public function reply(Request $request, PostPortal $post)
    {
        \Log::info('Reply attempt', ['user' => $request->user()->id, 'post' => $post->id, 'content' => $request->content]);

        $validated = $request->validate([
            'content' => 'required|max:5000', // Aumentado para suportar HTML
        ]);

        // Sanitize HTML content for reply
        $content = $this->sanitizeHtml($validated['content']);

        // Check if a reply with the same content was recently created
        $existingReply = PostPortal::where('parent_id', $post->id)
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

        $reply = new PostPortal();
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
     * Limpeza adicional do HTML para remover pontos após imagens e prevenir links indesejados
     *
     * @param string $html
     * @return string
     */
    protected function cleanupHtml($html)
    {
        // Se o HTML estiver vazio, retornar
        if (empty($html)) {
            return '';
        }

        // Remover links gerados automaticamente após imagens
        $html = preg_replace('/<img[^>]*>(\s*)<a[^>]*>([^<]*)<\/a>/', '<img$1>', $html);

        // Corrigir aspas extras nos links
        $html = str_replace('href="&quot;', 'href="', $html);
        $html = str_replace('&quot;"', '"', $html);

        // Evitar que URLs de imagem gerem metadados
        $html = preg_replace('/<a\s+href="([^"]*\.(?:jpg|jpeg|png|gif|webp))"[^>]*>([^<]*)<\/a>/', '$2', $html);

        // Remover qualquer menção a localhost como um link (mantendo o texto)
        $html = preg_replace('/<a[^>]*localhost[^>]*>([^<]*)<\/a>/', '$1', $html);

        // Remover cores e estilos de qualquer texto que contenha apenas um ponto
        $html = preg_replace('/<([a-z]+)[^>]*>\s*\.\s*<\/\1>/', '.', $html);

        // Corrigir problema em que a imagem pode estar dentro de tags extras
        $pattern = '/<([a-z]+)[^>]*>\s*(<img[^>]*>)\s*<\/\1>/i';
        $replacement = '$2';

        // Repete até que não haja mais alterações (para tratar aninhamentos)
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
            'sub'
        ];

        // Configurar HTMLPurifier
        if (class_exists('\HTMLPurifier')) {
            try {
                $config = \HTMLPurifier_Config::createDefault();

                // Definições importantes
                $config->set('Core.Encoding', 'UTF-8');
                $config->set('HTML.Doctype', 'HTML 4.01 Transitional');
                $config->set('Cache.SerializerPath', storage_path('app/purifier'));

                // Criar diretório de cache se não existir
                if (!file_exists(storage_path('app/purifier'))) {
                    mkdir(storage_path('app/purifier'), 0755, true);
                }

                // Configurações CSS
                $config->set('CSS.AllowedProperties', 'font,font-size,font-weight,font-style,font-family,text-decoration,padding-left,color,background-color,text-align');
                $config->set('CSS.AllowTricky', true);

                // Configurações de segurança
                $config->set('HTML.Trusted', true);
                $config->set('Core.EscapeInvalidTags', true);
                $config->set('HTML.SafeIframe', true);
                $config->set('URI.SafeIframeRegexp', '%^(https?:)?//(www\.youtube(?:-nocookie)?\.com/embed/|player\.vimeo\.com/video/)%');

                // Configurar tags e atributos permitidos
                $def = $config->getHTMLDefinition(true);

                // Permitir atributos src, alt, class para imagens
                $def->addAttribute('img', 'src', 'URI');
                $def->addAttribute('img', 'alt', 'Text');
                $def->addAttribute('img', 'class', 'CDATA');
                $def->addAttribute('img', 'width', 'Number');
                $def->addAttribute('img', 'height', 'Number');

                // Permitir atributos href, target para links
                $def->addAttribute('a', 'href', 'URI');
                $def->addAttribute('a', 'target', 'Enum#_blank,_self,_target,_top');
                $def->addAttribute('a', 'rel', 'CDATA');

                // Permitir atributos class e style para várias tags
                foreach ($allowedTags as $tag) {
                    $def->addAttribute($tag, 'class', 'CDATA');
                    $def->addAttribute($tag, 'style', 'CDATA');
                }

                // Executar purificação
                $purifier = new \HTMLPurifier($config);
                $clean = $purifier->purify($html);

                // Limpeza adicional para remover pontos após imagens e links indesejados
                return $this->cleanupHtml($clean);
            } catch (\Exception $e) {
                \Log::error('Erro no HTMLPurifier: ' . $e->getMessage());
                // Em caso de erro, fallback para strip_tags
                $stripped = strip_tags($html, '<' . implode('><', $allowedTags) . '>');
                return $this->cleanupHtml($stripped);
            }
        }

        // Fallback caso HTMLPurifier não esteja disponível
        $stripped = strip_tags($html, '<' . implode('><', $allowedTags) . '>');
        return $this->cleanupHtml($stripped);
    }

    public function destroy(PostPortal $post)
    {
        if (Gate::denies('delete', $post)) {
            abort(403);
        }

        $post->delete();

        return redirect()->back()->with('success', 'Post deleted successfully');
    }

    // Handle reply deletion
    public function destroyReply(PostPortal $reply)
    {
        $this->authorize('delete', $reply);
        $reply->delete();
        return redirect()->back();
    }
}
