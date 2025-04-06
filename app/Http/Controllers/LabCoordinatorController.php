<?php

namespace App\Http\Controllers;

use App\Models\PendingLabCoordinator;
use App\Models\Laboratory;
use App\Actions\Jetstream\CreateTeam;
use App\Actions\Jetstream\AddTeamMember;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class LabCoordinatorController extends Controller
{
    public function approve($token)
    {
        $pending = PendingLabCoordinator::where('token', $token)
            ->where('expires_at', '>', now())
            ->first();

        if (!$pending) {
            Log::warning('Tentativa de aprovação com token inválido ou expirado', ['token' => $token]);
            return redirect('/')->with('error', 'Link de aprovação inválido ou expirado.');
        }

        if ($pending->approved) {
            return redirect('/')->with('message', 'Este pedido já foi aprovado.');
        }

        $pending->update(['approved' => true]);
        $pending->user->profile->update(['lab_coordinator' => true]);

        // Create a team for the lab coordinator
        $laboratory = Laboratory::find($pending->laboratory_id);
        if ($laboratory) {
            $createTeam = new CreateTeam();
            $teamInput = [
                'name' => $laboratory->name,
                'address' => $pending->user->profile->institution->address ?? '',
            ];
            $team = $createTeam->create($pending->user, $teamInput);

            if ($team->personal_team) {
                $team->update(['personal_team' => false]);
            }

            $laboratory->team_id = $team->id;
            $laboratory->save();

            $pending->user->teams()->attach($team->id, ['created_at' => now(), 'updated_at' => now()]);

            Log::info('Team criado para o coordenador após aprovação', [
                'team_id' => $team->id,
                'user_id' => $pending->user->id,
                'laboratory_id' => $laboratory->id,
            ]);
        }

        Log::info('Coordenador de laboratório aprovado', [
            'user_id' => $pending->user_id,
            'laboratory_id' => $pending->laboratory_id,
        ]);

        return redirect('/')->with('message', 'Coordenador de laboratório aprovado com sucesso!');
    }

    public function reject($token)
    {
        $pending = PendingLabCoordinator::where('token', $token)
            ->where('expires_at', '>', now())
            ->first();

        if (!$pending) {
            Log::warning('Tentativa de rejeição com token inválido ou expirado', ['token' => $token]);
            return redirect('/')->with('error', 'Link de rejeição inválido ou expirado.');
        }

        if ($pending->approved) {
            return redirect('/')->with('message', 'Este pedido já foi processado.');
        }

        $pending->delete();
        $pending->user->profile->update(['lab_coordinator' => false]);

        Log::info('Solicitação de coordenador de laboratório rejeitada', [
            'user_id' => $pending->user_id,
            'laboratory_id' => $pending->laboratory_id,
        ]);

        return redirect('/')->with('message', 'Solicitação de coordenador de laboratório rejeitada.');
    }
}