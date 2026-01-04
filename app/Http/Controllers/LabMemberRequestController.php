<?php

namespace App\Http\Controllers;

use App\Models\PendingLabMember;
use App\Models\Team;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class LabMemberRequestController extends Controller
{
    public function store(Request $request, Team $team)
    {
        $user = Auth::user();

        $alreadyMember = $team->users()->where('user_id', $user->id)->exists();
        if ($alreadyMember) {
            return back()->with('message', 'Você já faz parte deste laboratório.');
        }

        $pendingExists = PendingLabMember::where('team_id', $team->id)
            ->where('user_id', $user->id)
            ->where('approved', false)
            ->where('expires_at', '>', now())
            ->exists();

        if ($pendingExists) {
            return back()->with('message', 'Você já possui um pedido pendente para este laboratório.');
        }

        $token = Str::random(60);
        PendingLabMember::create([
            'user_id' => $user->id,
            'team_id' => $team->id,
            'token' => $token,
            'expires_at' => now()->addDays(7),
        ]);

        $this->sendRequestEmail($team, $user->name, $user->email, $token);

        return back()->with('message', 'Pedido enviado ao coordenador.');
    }

    public function approve($token)
    {
        $pending = PendingLabMember::where('token', $token)
            ->where('expires_at', '>', now())
            ->first();

        if (!$pending) {
            return redirect('/')->with('error', 'Link inválido ou expirado.');
        }

        if ($pending->approved) {
            return redirect('/')->with('message', 'Este pedido já foi aprovado.');
        }

        $pending->update(['approved' => true]);

        $team = $pending->team;
        $user = $pending->user;

        if ($team && $user) {
            $team->users()->syncWithoutDetaching([$user->id => ['role' => 'editor']]);
        }

        return redirect('/')->with('message', 'Membro adicionado com sucesso!');
    }

    public function reject($token)
    {
        $pending = PendingLabMember::where('token', $token)
            ->where('expires_at', '>', now())
            ->first();

        if (!$pending) {
            return redirect('/')->with('error', 'Link inválido ou expirado.');
        }

        $pending->delete();

        return redirect('/')->with('message', 'Pedido rejeitado.');
    }

    protected function sendRequestEmail(Team $team, string $userName, string $userEmail, string $token): void
    {
        $approvalUrl = route('lab-members.approve', ['token' => $token]);
        $rejectionUrl = route('lab-members.reject', ['token' => $token]);

        $recipients = $team->users()->wherePivot('role', 'admin')->pluck('email')->toArray();
        if (empty($recipients) && $team->owner) {
            $recipients = [$team->owner->email];
        }

        if (empty($recipients)) {
            $recipients = ['contato@portalmetabolomicabrasil.com.br'];
        }

        Mail::raw(
            "Um usuário solicitou entrar como membro no laboratório {$team->name}.\n" .
            "Nome: {$userName}\n" .
            "Email: {$userEmail}\n" .
            "Aprovar: {$approvalUrl}\n" .
            "Rejeitar: {$rejectionUrl}\n" .
            "Este link expira em 7 dias.",
            function ($message) use ($recipients, $team) {
                $message->to($recipients)
                    ->subject("Solicitação de Membro: {$team->name}")
                    ->from(config('mail.from.address'), config('mail.from.name'));
            }
        );
    }
}
