<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    protected $fillable = [
        'content',
        'tag',
        'additional_tags',
        'metadata',
        'parent_id',
        'user_id',
        'is_lab_publication'
    ];

    protected $casts = [
        'additional_tags' => 'array',
        'metadata' => 'array',
        'is_lab_publication' => 'boolean'
    ];

    // Accessor to get all tags
    public function getAllTagsAttribute()
    {
        $tags = [$this->tag];
        if (!empty($this->additional_tags)) {
            $tags = array_merge($tags, $this->additional_tags);
        }
        return $tags;
    }

    // Relationship with user
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relationship for replies
    public function replies()
    {
        return $this->hasMany(Post::class, 'parent_id')->latest();
    }
}
