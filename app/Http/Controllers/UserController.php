<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index()
    {
        // Fetch all users with their teams
        $users = User::with('teams')->get();

        // Ensure owners are also part of the team members
        foreach ($users as $user) {
            foreach ($user->ownedTeams as $ownedTeam) {
                if (!$user->teams->contains($ownedTeam)) {
                    $user->teams->push($ownedTeam);
                }
            }
        }

        return view('membros', ['users' => $users]);
    }
}
