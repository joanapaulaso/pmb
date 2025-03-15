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
        'team_id',
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
            if ($laboratory->team_id) {
                $existing = self::where('name', $laboratory->name)
                    ->where('institution_id', $laboratory->institution_id)
                    ->where('state_id', $laboratory->state_id)
                    ->whereNull('team_id')
                    ->first();

                if ($existing) {
                    $existing->update(['team_id' => $laboratory->team_id]);
                    return false;
                }
            }
        });
    }

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

    public function profiles()
    {
        return $this->hasMany(Profile::class);
    }

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

    // Acessores para puxar dados de teams
    public function getDescriptionAttribute()
    {
        return $this->team->description ?? null;
    }

    public function getWebsiteAttribute()
    {
        return $this->team->website ?? null;
    }

    public function getAddressAttribute()
    {
        return $this->team->address ?? null;
    }

    public function getLatAttribute()
    {
        return $this->team->latitude ?? null;
    }

    public function getLngAttribute()
    {
        return $this->team->longitude ?? null;
    }

    public function getLogoAttribute()
    {
        return $this->team->logo ?? null;
    }

    public function getBuildingAttribute()
    {
        return $this->team->building ?? null;
    }

    public function getFloorAttribute()
    {
        return $this->team->floor ?? null;
    }

    public function getRoomAttribute()
    {
        return $this->team->room ?? null;
    }

    public function getDepartmentAttribute()
    {
        return $this->team->department ?? null;
    }

    public function getCampusAttribute()
    {
        return $this->team->campus ?? null;
    }

    public function getPhoneAttribute()
    {
        return $this->team->phone ?? null;
    }

    public function getContactEmailAttribute()
    {
        return $this->team->contact_email ?? null;
    }

    public function getWorkingHoursAttribute()
    {
        return $this->team->working_hours ?? null;
    }

    public function getHasAccessibilityAttribute()
    {
        return $this->team->has_accessibility ?? false;
    }
}
