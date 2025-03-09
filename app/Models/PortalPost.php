<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PortalPost extends Model
{
    protected $fillable = ['content', 'media', 'media_type', 'pinned', 'user_id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
