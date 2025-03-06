<?php

namespace App\Http\Controllers;

use App\Models\Team;
use Illuminate\Support\Facades\Gate;

class TeamController extends Controller
{
    public function show(Team $team)
    {
        if (!Gate::check('update', $team)) {
            abort(403, 'Unauthorized action.');
        }

        return view('teams.show', compact('team'));
    }
}
