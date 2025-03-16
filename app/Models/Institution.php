<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Institution extends Model
{
    protected $fillable = ['name', 'state_id', 'municipality_id', 'country_code', 'address'];

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
            if ($institution->country_code === 'BR') {
                if (is_null($institution->state_id) || is_null($institution->municipality_id)) {
                    throw new \Exception("Para instituições no Brasil, estado e município são obrigatórios.");
                }

                if (
                    Institution::where('name', $institution->name)
                        ->where('state_id', $institution->state_id)
                        ->exists()
                ) {
                    throw new \Exception("Já existe uma instituição com esse nome no estado selecionado.");
                }
            } else {
                // Para instituições internacionais, estado e município devem ser nulos
                $institution->state_id = null;
                $institution->municipality_id = null;
            }
        });

        static::updating(function ($institution) {
            if ($institution->country_code === 'BR') {
                if (is_null($institution->state_id) || is_null($institution->municipality_id)) {
                    throw new \Exception("Para instituições no Brasil, estado e município são obrigatórios.");
                }
            } else {
                $institution->state_id = null;
                $institution->municipality_id = null;
            }
        });
    }
}
