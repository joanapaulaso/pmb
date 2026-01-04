<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class MessageController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $selectedUserId = $request->query('user');
        $isMobile = Str::contains(strtolower($request->header('User-Agent', '')), ['mobile', 'android', 'iphone']);

        $conversationUserIds = Message::where(function ($query) use ($user) {
            $query->where('sender_id', $user->id)
                  ->orWhere('recipient_id', $user->id);
        })
        ->selectRaw('DISTINCT (CASE WHEN sender_id = ? THEN recipient_id ELSE sender_id END) as other_user_id', [$user->id])
        ->pluck('other_user_id')
        ->filter();

        $conversationUsers = User::whereIn('id', $conversationUserIds)->orderBy('name')->get();

        // Em desktop, se não houver seleção e não estiver forçando a lista, abre o primeiro chat
        if (!$selectedUserId && $conversationUsers->isNotEmpty() && !$request->boolean('list') && !$isMobile) {
            $selectedUserId = $conversationUsers->first()->id;
        }

        $thread = collect();

        if ($selectedUserId) {
            $thread = Message::with(['sender', 'recipient'])
                ->where(function ($query) use ($user, $selectedUserId) {
                    $query->where('sender_id', $user->id)
                          ->where('recipient_id', $selectedUserId);
                })
                ->orWhere(function ($query) use ($user, $selectedUserId) {
                    $query->where('sender_id', $selectedUserId)
                          ->where('recipient_id', $user->id);
                })
                ->orderBy('created_at')
                ->get();

            // Mark as read
            Message::where('recipient_id', $user->id)
                ->where('sender_id', $selectedUserId)
                ->whereNull('read_at')
                ->update(['read_at' => now()]);
        }

        $unreadCounts = Message::selectRaw('CASE WHEN sender_id = ? THEN recipient_id ELSE sender_id END as other_user_id, COUNT(*) as unread_count', [$user->id])
            ->whereNull('read_at')
            ->where('recipient_id', $user->id)
            ->groupBy('other_user_id')
            ->pluck('unread_count', 'other_user_id');

        $recipients = User::where('id', '!=', $user->id)->orderBy('name')->get(['id', 'name', 'email']);

        return view('messages.index', [
            'conversationUsers' => $conversationUsers,
            'selectedUserId' => $selectedUserId,
            'thread' => $thread,
            'unreadCounts' => $unreadCounts,
            'recipients' => $recipients,
            'isMobile' => $isMobile,
        ]);
    }

    public function store(Request $request)
    {
        $user = Auth::user();

        $data = $request->validate([
            'recipient_id' => ['required', 'exists:users,id', 'different:' . $user->id],
            'body' => ['required', 'string'],
        ]);

        Message::create([
            'sender_id' => $user->id,
            'recipient_id' => $data['recipient_id'],
            'body' => trim($data['body']),
        ]);

        return redirect()->route('messages.index', ['user' => $data['recipient_id']])
            ->with('message', 'Mensagem enviada!');
    }
}
