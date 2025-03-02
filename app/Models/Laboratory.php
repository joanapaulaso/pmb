<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Laboratory extends Model
{
    protected $fillable = ['name', 'institution_id', 'state_id'];

    public function institution()
    {
        return $this->belongsTo(Institution::class);
    }

    public function state()
    {
        return $this->belongsTo(State::class);
    }
}
