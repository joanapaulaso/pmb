<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Profile extends Model
{
    protected $fillable = [
        'user_id',
        'birth_date',
        'institution_id',
        'laboratory_id',
        'lab_coordinator',
        'municipality_id',
        'country_code',
        'state_id',
        'profile_picture',
        'gender'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function institution()
    {
        return $this->belongsTo(Institution::class);
    }

    public function laboratory()
    {
        return $this->belongsTo(Laboratory::class);
    }

    public function municipality()
    {
        return $this->belongsTo(Municipality::class);
    }

    public function state()
    {
        return $this->belongsTo(State::class);
    }

    public function country()
    {
        return $this->belongsTo(Country::class, 'country_code', 'code');
    }
}
