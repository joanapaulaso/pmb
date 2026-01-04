<?php

namespace App\Http\Controllers;

use App\Models\Equipment;
use Illuminate\Support\Facades\Log;

class EquipmentMapController extends Controller
{
    public function index()
    {
        $equipments = Equipment::with(['team.users.profile'])
            ->get()
            ->map(function (Equipment $equipment) {
                $coordinator = $equipment->team?->users
                    ->first(function ($user) {
                        return optional($user->profile)->lab_coordinator;
                    });

                $contactEmail = $coordinator?->email
                    ?? $equipment->team?->contact_email
                    ?? $equipment->team?->owner?->email
                    ?? null;

                return [
                    'id' => $equipment->id,
                    'title' => $equipment->title ?: $equipment->model,
                    'model' => $equipment->model,
                    'brand' => $equipment->brand,
                    'lab_name' => $equipment->team?->name,
                    'lab_id' => $equipment->team?->id,
                    'available_for_services' => (bool) $equipment->available_for_services,
                    'available_for_collaboration' => (bool) $equipment->available_for_collaboration,
                    'contact_email' => $contactEmail,
                ];
            })
            ->values();

        Log::info('Equipamentos carregados para o Mapa de Equipamentos', ['count' => $equipments->count()]);

        return view('equipments.map', ['equipments' => $equipments]);
    }
}
