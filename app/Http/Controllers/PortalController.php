<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PostPortal;

class PortalController extends Controller
{
    /**
     * Exibe o dashboard com posts
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        // Obter cores das tags da configuração
        $tagColors = config('tags.colors', []);
        $tags = array_keys($tagColors);

        // if (empty($tags)) {
        //     $tags = ['all', 'general', 'question', 'job', 'promotion', 'idea', 'collaboration', 'news', 'paper'];
        // }

        // Iniciar query base
        $query = PostPortal::whereNull('parent_id') // Apenas posts principais, não respostas
            ->with(['user', 'replies.user']) // Carrega respostas e usuários em uma única consulta
            ->latest();

        // Processar tags selecionadas
        $selectedTags = $request->input('tags', []);

        if (!is_array($selectedTags)) {
            $selectedTags = array_filter(explode(',', $selectedTags));
        }

        // Limitar a 3 tags selecionadas para evitar consultas muito complexas
        $selectedTags = array_slice($selectedTags, 0, 3);

        // Aplicar filtro de tags se necessário
        if (!empty($selectedTags) && !in_array('all', $selectedTags)) {
            $query->where(function ($q) use ($selectedTags) {
                $q->whereIn('tag', $selectedTags)
                    ->orWhereJsonContains('additional_tags', $selectedTags);
            });
        }

        // Adicionar limites e paginação para evitar timeout
        $posts = $query->limit(20)->get();

        return view('portal', compact('posts', 'tags', 'selectedTags', 'tagColors'));
    }

    /**
     * Filtra os posts baseado em tags (para requisições AJAX/JSON)
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|\Illuminate\View\View
     */
    public function filter(Request $request)
    {
        // Obter cores das tags
        $tagColors = config('tags.colors', []);
        $tags = array_keys($tagColors);

        // if (empty($tags)) {
        //     $tags = ['all', 'general', 'question', 'job', 'promotion', 'idea', 'collaboration', 'news', 'paper'];
        // }

        // Iniciar a consulta com posts principais
        $query = PostPortal::whereNull('parent_id')
            ->with(['user', 'replies.user'])
            ->latest();

        // Processar tags selecionadas
        $selectedTags = $request->input('tags', []);

        if (!is_array($selectedTags)) {
            $selectedTags = array_filter(explode(',', $selectedTags));
        }

        // Limitar a 3 tags selecionadas
        $selectedTags = array_slice($selectedTags, 0, 3);

        // Aplicar filtro de tags
        if (!empty($selectedTags) && !in_array('all', $selectedTags)) {
            $query->where(function ($q) use ($selectedTags) {
                $q->whereIn('tag', $selectedTags)
                    ->orWhereJsonContains('additional_tags', $selectedTags);
            });
        }

        // Verificar se esperamos JSON (para AJAX)
        if ($request->expectsJson()) {
            $posts = $query->limit(20)->get()->map(function ($post) use ($tagColors) {
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
                    'metadata' => $post->metadata ?? [],
                    'created_at_diff' => $post->created_at->diffForHumans(),
                    'can_delete' => auth()->check() && auth()->user()->can('delete', $post),
                    'reply_url' => route('posts.reply', $post),
                    'replies' => $post->replies->map(function ($reply) {
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

            return response()->json(['posts' => $posts]);
        }

        // Para requisições normais, retornar a view
        $posts = $query->limit(20)->get();

        return view('portal', compact('posts', 'tags', 'selectedTags', 'tagColors'));
    }
}
