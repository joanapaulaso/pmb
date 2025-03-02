<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Institution extends Model
{
    protected $fillable = ['name', 'state_id', 'municipality_id', 'country_code'];

    public function state()
    {
        return $this->belongsTo(State::class);
    }

    public function municipality()
    {
        return $this->belongsTo(Municipality::class);
    }

    public function country()
    {
        return $this->belongsTo(Country::class, 'country_code', 'code');
    }

    public static function boot()
    {
        parent::boot();
        static::creating(function ($institution) {
            // Garante que não criamos instituições duplicadas no mesmo estado
            if (Institution::where('name', $institution->name)
                ->where('state_id', $institution->state_id)
                ->exists()
            ) {
                throw new \Exception("Já existe uma instituição com esse nome no estado selecionado.");
            }
        });
    }
}
