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

    /**
     * As regras de unicidade para laboratórios.
     */
    public static function uniqueRules($ignoreId = null)
    {
        $rules = [
            'name' => 'required|string|max:255',
            'institution_id' => 'required|exists:institutions,id',
            'state_id' => 'required|exists:states,id',
            'team_id' => 'nullable|exists:teams,id',
        ];

        return $rules;
    }

    /**
     * Booted method para definir os eventos do modelo
     */
    protected static function booted()
    {
        static::creating(function ($laboratory) {
            // Verificar se já existe um laboratório com esse nome, instituição e estado
            // Se existir um com team_id nulo e estamos tentando criar um com team_id,
            // podemos atualizar o existente em vez de criar um novo
            if ($laboratory->team_id) {
                $existing = self::where('name', $laboratory->name)
                    ->where('institution_id', $laboratory->institution_id)
                    ->where('state_id', $laboratory->state_id)
                    ->whereNull('team_id')
                    ->first();

                if ($existing) {
                    $existing->update(['team_id' => $laboratory->team_id]);
                    return false; // Impede a criação e usa o existente
                }
            }
        });
    }

    /**
     * Relacionamento com a instituição
     */
    public function institution()
    {
        return $this->belongsTo(Institution::class);
    }

    /**
     * Relacionamento com o estado
     */
    public function state()
    {
        return $this->belongsTo(State::class);
    }

    /**
     * Relacionamento com a equipe/time
     */
    public function team()
    {
        return $this->belongsTo(Team::class);
    }

    /**
     * Relacionamento com perfis
     */
    public function profiles()
    {
        return $this->hasMany(Profile::class);
    }

    /**
     * Escopo para pesquisa avançada
     */
    public function scopeSearch($query, $term)
    {
        if ($term) {
            $query->where('name', 'LIKE', "%{$term}%")
                ->orWhereHas('institution', function ($q) use ($term) {
                    $q->where('name', 'LIKE', "%{$term}%");
                })
                ->orWhereHas('state', function ($q) use ($term) {
                    $q->where('name', 'LIKE', "%{$term}%");
                });
        }
        return $query;
    }
}
