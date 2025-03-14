<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Playlist extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'title',
        'description',
        'thumbnail',
        'published',
        'featured'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'published' => 'boolean',
        'featured' => 'boolean',
    ];

    /**
     * Get the videos for the playlist.
     */
    public function videos()
    {
        return $this->hasMany(Video::class)->orderBy('created_at', 'desc');
    }

    /**
     * Get the thumbnail URL.
     *
     * @return string
     */
    public function getThumbnailUrlAttribute()
    {
        if ($this->thumbnail) {
            return asset('storage/' . $this->thumbnail);
        }

        // Try to get the first video's thumbnail
        $firstVideo = $this->videos()->first();
        if ($firstVideo) {
            return $firstVideo->thumbnail_url;
        }

        return asset('images/default-playlist-thumbnail.jpg');
    }

    /**
     * Get the number of videos in this playlist.
     *
     * @return int
     */
    public function getVideoCountAttribute()
    {
        return $this->videos()->count();
    }
}
