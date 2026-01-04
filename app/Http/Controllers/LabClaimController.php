<?php

namespace App\Http\Controllers;

use App\Models\PendingLabClaim;
use App\Models\Team;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class LabClaimController extends Controller
{
    public function store(Request $request, Team $team)
    {
        $user = Auth::user();

        if ($team->is_claimed) {
            return back()->with('error', 'Este laboratório já foi reivindicado.');
        }

        $existing = PendingLabClaim::where('team_id', $team->id)
            ->where('user_id', $user->id)
            ->where('approved', false)
            ->where('expires_at', '>', now())
            ->first();

        if ($existing) {
            return back()->with('message', 'Você já possui um pedido de reivindicação pendente para este laboratório.');
        }

        $token = Str::random(60);
        PendingLabClaim::create([
            'user_id' => $user->id,
            'team_id' => $team->id,
            'token' => $token,
            'expires_at' => now()->addDays(7),
        ]);

        $this->sendClaimEmail($user->name, $user->email, $team->name, $token);

        return back()->with('message', 'Pedido enviado. Aguarde a aprovação do admin.');
    }

    public function approve($token)
    {
        $pending = PendingLabClaim::where('token', $token)
            ->where('expires_at', '>', now())
            ->first();

        if (!$pending) {
            return redirect('/')->with('error', 'Link de aprovação inválido ou expirado.');
        }

        if ($pending->approved) {
            return redirect('/')->with('message', 'Este pedido já foi aprovado.');
        }

        $pending->update(['approved' => true]);

        $team = $pending->team;
        $user = $pending->user;

        if ($team) {
            $team->is_claimed = true;
            // Sempre associe o user_id ao coordenador aprovado
            $team->user_id = $user->id;
            $team->save();

            $team->users()->syncWithoutDetaching([$user->id => ['role' => 'admin']]);

            $profile = $user->profile;
            if ($profile) {
                $profile->lab_coordinator = true;
                $profile->laboratory_id = $profile->laboratory_id ?? null;
                $profile->save();
            }
        }

        return redirect('/')->with('message', 'Reivindicação aprovada com sucesso!');
    }

    public function reject($token)
    {
        $pending = PendingLabClaim::where('token', $token)
            ->where('expires_at', '>', now())
            ->first();

        if (!$pending) {
            return redirect('/')->with('error', 'Link de rejeição inválido ou expirado.');
        }

        $pending->delete();

        return redirect('/')->with('message', 'Reivindicação rejeitada.');
    }

    protected function sendClaimEmail(string $userName, string $userEmail, string $teamName, string $token): void
    {
        $approvalUrl = route('lab-claims.approve', ['token' => $token]);
        $rejectionUrl = route('lab-claims.reject', ['token' => $token]);

        Mail::raw(
            "Um usuário solicitou ser coordenador do laboratório {$teamName}.\n" .
            "Nome: {$userName}\n" .
            "Email: {$userEmail}\n" .
            "Aprovar: {$approvalUrl}\n" .
            "Rejeitar: {$rejectionUrl}\n" .
            "Este link expira em 7 dias.",
            function ($message) use ($userName) {
                $message->to('contato@portalmetabolomicabrasil.com.br')
                    ->subject("Reivindicação de Laboratório: {$userName}")
                    ->from(config('mail.from.address'), config('mail.from.name'));
            }
        );

        Log::info('Email de reivindicação de laboratório enviado', [
            'user' => $userName,
            'team' => $teamName,
            'token' => $token,
        ]);
    }
}
