<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PendingLabClaim extends Model
{
    protected $fillable = [
        'user_id',
        'team_id',
        'token',
        'approved',
        'expires_at',
    ];

    protected $casts = [
        'approved' => 'boolean',
        'expires_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function team()
    {
        return $this->belongsTo(Team::class);
    }
}
