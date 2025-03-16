<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Laravel\Jetstream\Events\TeamCreated;
use Laravel\Jetstream\Events\TeamDeleted;
use Laravel\Jetstream\Events\TeamUpdated;
use Laravel\Jetstream\Team as JetstreamTeam;

class Team extends JetstreamTeam
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'personal_team',
        'description',
        'address',
        'latitude',
        'longitude',
        // Campos de localização física
        'building',
        'floor',
        'room',
        // Informações de contato
        'phone',
        'contact_email',
        'contact_person',
        // Informações complementares
        'complement',
        'reference_point',
        'postal_code',
        // Informações operacionais
        'working_hours',
        'website',
        'has_accessibility',
        // Campo para observações
        'address_notes',
        // Campos adicionais
        'department',
        'campus',
    ];

    /**
     * The event map for the model.
     *
     * @var array<string, class-string>
     */
    protected $dispatchesEvents = [
        'created' => TeamCreated::class,
        'updated' => TeamUpdated::class,
        'deleted' => TeamDeleted::class,
    ];

    /**
     * The attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'personal_team' => 'boolean',
            'has_accessibility' => 'boolean',
            'latitude' => 'float',
            'longitude' => 'float',
        ];
    }

    /**
     * The users that belong to the team.
     */
    public function users()
    {
        return $this->belongsToMany(User::class, 'team_user')->withPivot('role')->withTimestamps();
    }

    /**
     * The owner of the team.
     */
    public function owner()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function equipments()
    {
        return $this->hasMany(Equipment::class);
    }

    /**
     * Get the full formatted address with complement.
     *
     * @return string
     */
    public function getFormattedAddressAttribute()
    {
        $parts = [$this->address];

        if ($this->complement) {
            $parts[] = $this->complement;
        }

        if ($this->building) {
            $parts[] = "Prédio: {$this->building}";
        }

        if ($this->floor) {
            $parts[] = "Andar: {$this->floor}";
        }

        if ($this->room) {
            $parts[] = "Sala: {$this->room}";
        }

        return implode(', ', array_filter($parts));
    }

    /**
     * Verifica se o modelo tem coordenadas geográficas válidas
     *
     * @return bool
     */
    public function hasValidCoordinates()
    {
        return !is_null($this->latitude) &&
            !is_null($this->longitude) &&
            $this->latitude != 0 &&
            $this->longitude != 0;
    }

    /**
     * Retorna as coordenadas como um array
     *
     * @return array|null
     */
    public function getCoordinates()
    {
        if ($this->hasValidCoordinates()) {
            return [
                'lat' => (float) $this->latitude,
                'lng' => (float) $this->longitude
            ];
        }

        return null;
    }
}
