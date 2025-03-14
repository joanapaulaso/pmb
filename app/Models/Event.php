<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Event extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'title',
        'slug',
        'description',
        'event_type',
        'start_date',
        'end_date',
        'location',
        'online_url',
        'registration_url',
        'image',
        'is_featured',
        'is_published'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'is_featured' => 'boolean',
        'is_published' => 'boolean',
    ];

    /**
     * Get the speakers for the event.
     */
    public function speakers()
    {
        return $this->hasMany(Speaker::class);
    }

    /**
     * Get the image URL.
     *
     * @return string
     */
    public function getImageUrlAttribute()
    {
        if ($this->image) {
            return asset('storage/' . $this->image);
        }

        return asset('images/default-event-image.jpg');
    }

    /**
     * Check if the event is in the future.
     *
     * @return bool
     */
    public function getIsUpcomingAttribute()
    {
        return $this->start_date > Carbon::now();
    }

    /**
     * Check if the event is currently running.
     *
     * @return bool
     */
    public function getIsOngoingAttribute()
    {
        $now = Carbon::now();
        return $this->start_date <= $now && $this->end_date >= $now;
    }

    /**
     * Check if the event is in the past.
     *
     * @return bool
     */
    public function getIsPastAttribute()
    {
        return $this->end_date < Carbon::now();
    }

    /**
     * Format the date range for display.
     *
     * @return string
     */
    public function getFormattedDateRangeAttribute()
    {
        $startDate = $this->start_date->format('d/m/Y');
        $endDate = $this->end_date->format('d/m/Y');

        if ($startDate === $endDate) {
            return $startDate . ' ' . $this->start_date->format('H:i') . ' - ' . $this->end_date->format('H:i');
        }

        return $startDate . ' - ' . $endDate;
    }

    /**
     * Scope a query to only include published events.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }

    /**
     * Scope a query to only include featured events.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    /**
     * Scope a query to only include upcoming events.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeUpcoming($query)
    {
        return $query->where('start_date', '>', Carbon::now());
    }

    /**
     * Scope a query to only include past events.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopePast($query)
    {
        return $query->where('end_date', '<', Carbon::now());
    }
}
