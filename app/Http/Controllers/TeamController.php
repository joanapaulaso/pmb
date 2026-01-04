<?php

namespace App\Http\Controllers;

use App\Models\Team;
use Illuminate\Support\Facades\Gate;

class TeamController extends Controller
{
    /**
     * Redireciona a página pública do laboratório para quem não é coordenador.
     */
    public function public(Team $team)
    {
        // Qualquer usuário autenticado pode ver a página pública
        return redirect()->route('labs.show', $team->id);
    }

    public function show(Team $team)
    {
        if (! Gate::check('update', $team)) {
            // Usuários que não são coordenadores devem ser redirecionados
            // para a página pública do laboratório.
            return redirect()->route('labs.show', $team->id);
        }

        return view('teams.show', compact('team'));
    }
}
