<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PortalPost;

class PortalController extends Controller
{
    public function index()
    {
        $posts = PortalPost::orderBy('pinned', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();
        \Log::info('Posts carregados:', ['count' => $posts->count()]);
        return view('portal', compact('posts'));
    }

    public function store(Request $request)
    {
        if (!auth()->check()) {
            \Log::warning('Usuário não autenticado ao tentar salvar post.');
            return redirect()->route('login')->with('error', 'Você precisa estar logado!');
        }

        \Log::info('Dados recebidos no store:', $request->all());

        try {
            $validated = $request->validate([
                'content' => 'required|string|max:5000',
                'media' => 'nullable|file|mimes:jpg,png,mp4|max:10240'
            ]);

            $post = new PortalPost();
            $post->content = $request->content;
            $post->user_id = auth()->id();

            if ($request->hasFile('media')) {
                \Log::info('Salvando mídia...', ['file' => $request->file('media')->getClientOriginalName()]);
                $path = $request->file('media')->store('public/posts');
                $post->media = $path;
                $post->media_type = $request->file('media')->getClientOriginalExtension() === 'mp4' ? 'video' : 'image';
            }

            $post->save();
            \Log::info('Post salvo:', $post->toArray());

            return redirect()->route('portal')->with('success', 'Postagem criada com sucesso!');
        } catch (\Exception $e) {
            \Log::error('Erro ao salvar post:', ['error' => $e->getMessage()]);
            return redirect()->back()->withErrors(['content' => 'Erro ao salvar a postagem: ' . $e->getMessage()])->withInput();
        }
    }

    public function togglePin(PortalPost $post)
    {
        $post->pinned = !$post->pinned;
        $post->save();
        return redirect()->route('portal')->with('success', 'Postagem atualizada!');
    }
}
