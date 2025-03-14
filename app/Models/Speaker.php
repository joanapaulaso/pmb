<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Speaker extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'event_id',
        'name',
        'bio',
        'photo',
        'institution',
        'role'
    ];

    /**
     * Get the event that owns the speaker.
     */
    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    /**
     * Get the photo URL.
     *
     * @return string
     */
    public function getPhotoUrlAttribute()
    {
        if ($this->photo) {
            return asset('storage/' . $this->photo);
        }

        return asset('images/default-speaker-photo.jpg');
    }
}
