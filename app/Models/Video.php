<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Video extends Model
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
        'url',
        'thumbnail',
        'playlist_id',
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
     * Get the playlist that owns the video.
     */
    public function playlist()
    {
        return $this->belongsTo(Playlist::class);
    }

    /**
     * Get YouTube video ID from URL.
     *
     * @return string|null
     */
    public function getYoutubeIdAttribute()
    {
        if (preg_match('/youtube\.com\/watch\?v=([^\&\?\/]+)/', $this->url, $match)) {
            return $match[1];
        } elseif (preg_match('/youtube\.com\/embed\/([^\&\?\/]+)/', $this->url, $match)) {
            return $match[1];
        } elseif (preg_match('/youtube\.com\/v\/([^\&\?\/]+)/', $this->url, $match)) {
            return $match[1];
        } elseif (preg_match('/youtu\.be\/([^\&\?\/]+)/', $this->url, $match)) {
            return $match[1];
        }

        return null;
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

        // Use YouTube thumbnail if available
        if ($this->youtube_id) {
            return 'https://img.youtube.com/vi/' . $this->youtube_id . '/mqdefault.jpg';
        }

        return asset('images/default-video-thumbnail.jpg');
    }

    /**
     * Get the embed URL for the video.
     *
     * @return string|null
     */
    public function getEmbedUrlAttribute()
    {
        if ($this->youtube_id) {
            return 'https://www.youtube.com/embed/' . $this->youtube_id;
        }

        return null;
    }
}
