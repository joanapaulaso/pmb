<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Profile extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'birth_date',
        'gender',
        'country_code',
        'state_id',
        'municipality_id',
        'institution_id',
        'laboratory_id',
        'lab_coordinator',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'birth_date' => 'date',
        'lab_coordinator' => 'boolean',
    ];

    /**
     * Relacionamento com o usuário
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relacionamento com o país
     */
    public function country()
    {
        return $this->belongsTo(Country::class, 'country_code', 'code');
    }

    /**
     * Relacionamento com o estado
     */
    public function state()
    {
        return $this->belongsTo(State::class);
    }

    /**
     * Relacionamento com o município
     */
    public function municipality()
    {
        return $this->belongsTo(Municipality::class);
    }

    /**
     * Relacionamento com a instituição
     */
    public function institution()
    {
        return $this->belongsTo(Institution::class);
    }

    /**
     * Relacionamento com o laboratório
     */
    public function laboratory()
    {
        return $this->belongsTo(Laboratory::class);
    }
}
