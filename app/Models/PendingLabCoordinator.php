<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PendingLabCoordinator extends Model
{
    protected $fillable = [
        'user_id',
        'laboratory_id',
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

    public function laboratory()
    {
        return $this->belongsTo(Laboratory::class);
    }
}