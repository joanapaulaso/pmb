<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Equipment extends Model
{
    protected $table = 'equipments';

    protected $fillable = [
            'team_id',
            'title',
            'model',
            'brand',
            'technical_responsible',
            'available_for_services',
            'available_for_collaboration',
            'photo_path',
        ];

    protected $casts = [
        'available_for_services' => 'boolean',
        'available_for_collaboration' => 'boolean',
    ];

    public function team()
    {
        return $this->belongsTo(Team::class);
    }
}
