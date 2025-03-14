<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{
    public function index(Request $request)
    {
        // Obter os parâmetros de ordenação da requisição
        $sort = $request->input('sort', 'name'); // Coluna para ordenar, padrão: nome
        $direction = $request->input('direction', 'asc'); // Direção, padrão: crescente

        // Validar direção de ordenação
        $direction = in_array(strtolower($direction), ['asc', 'desc']) ? $direction : 'asc';

        // Iniciar a consulta
        $query = User::with([
            'teams',
            'ownedTeams',
            'profile',
            'profile.laboratory',
            'profile.institution',
            'profile.state'
        ]);

        // Aplicar ordenação com base no campo solicitado
        switch ($sort) {
            case 'laboratory':
                $query->join('profiles', 'users.id', '=', 'profiles.user_id')
                    ->leftJoin('laboratories', 'profiles.laboratory_id', '=', 'laboratories.id')
                    ->orderBy('laboratories.name', $direction)
                    ->select('users.*');
                break;

            case 'institution':
                $query->join('profiles', 'users.id', '=', 'profiles.user_id')
                    ->leftJoin('institutions', 'profiles.institution_id', '=', 'institutions.id')
                    ->orderBy('institutions.name', $direction)
                    ->select('users.*');
                break;

            case 'state':
                $query->join('profiles', 'users.id', '=', 'profiles.user_id')
                    ->leftJoin('states', 'profiles.state_id', '=', 'states.id')
                    ->orderBy('states.name', $direction)
                    ->select('users.*');
                break;

            case 'name':
            default:
                $query->orderBy('name', $direction);
                break;
        }

        // Executar a consulta com paginação
        $users = $query->paginate(20);

        // Manter os parâmetros de ordenação na paginação
        $users->appends(['sort' => $sort, 'direction' => $direction]);

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

        return view('membros', [
            'users' => $users,
            'sort' => $sort,
            'direction' => $direction
        ]);
    }
}
