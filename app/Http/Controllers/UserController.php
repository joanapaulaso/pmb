<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{
    public function index()
    {
        // Fetch all users with their teams
        $users = User::with('teams')->get();

        // Ensure owners are also part of the team members
        foreach ($users as $user) {
            Log::info('Usuário carregado:', ['user_id' => $user->id, 'teams' => $user->teams->pluck('name')->toArray()]);
            foreach ($user->ownedTeams as $ownedTeam) {
                if (!$user->teams->contains($ownedTeam)) {
                    $user->teams->push($ownedTeam);
                    Log::info('Time owned adicionado:', ['user_id' => $user->id, 'team_name' => $ownedTeam->name]);
                }
            }
        }

        return view('membros', ['users' => $users]);
    }
}
