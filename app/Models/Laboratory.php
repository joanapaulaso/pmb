<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Laboratory extends Model
{

    use HasFactory;

    protected $fillable = [
        'name',
        'institution_id',
        'state_id',
        'team_id'
    ];

    public function institution()
    {
        return $this->belongsTo(Institution::class);
    }

    public function state()
    {
        return $this->belongsTo(State::class);
    }

    public function team()
    {
        return $this->belongsTo(Team::class);
    }
}
