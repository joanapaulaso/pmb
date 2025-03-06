<?php

namespace App\Http\Controllers;

use App\Models\Team;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class LabsMapController extends Controller
{
    /**
     * Exibe a página do mapa de laboratórios
     */
    public function index()
    {
        // Log para debug
        Log::info('Iniciando busca de laboratórios para o mapa');

        // Consulta inicial - sem filtros para ver quantos laboratórios temos no total
        $totalLabs = Team::count();
        Log::info('Total de laboratórios no sistema: ' . $totalLabs);

        // Verificar quantos têm coordenadas
        $labsWithCoordinates = Team::whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->count();
        Log::info('Laboratórios com alguma coordenada: ' . $labsWithCoordinates);

        // Verificar quantos têm coordenadas não-zero
        $labsWithValidCoords = Team::whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->where('latitude', '!=', 0)
            ->where('longitude', '!=', 0)
            ->count();
        Log::info('Laboratórios com coordenadas válidas (não-zero): ' . $labsWithValidCoords);

        // Obter apenas as equipes (laboratórios) com coordenadas válidas
        $labs = Team::whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->where('latitude', '!=', 0)
            ->where('longitude', '!=', 0)
            ->get();

        Log::info('Laboratórios encontrados: ' . $labs->count());

        // Para cada laboratório, fazer log das coordenadas
        foreach ($labs as $lab) {
            Log::info("Lab {$lab->id} - {$lab->name}: lat={$lab->latitude}, lng={$lab->longitude}");
        }

        // Formatação para o mapa
        $formattedLabs = $labs->map(function ($lab) {
            return [
                'id' => $lab->id,
                'name' => $lab->name,
                'address' => $lab->address,
                'formatted_address' => $lab->formatted_address ?? $lab->address,
                'coordinates' => [
                    'lat' => (float) $lab->latitude,
                    'lng' => (float) $lab->longitude
                ],
                'details' => [
                    'building' => $lab->building,
                    'floor' => $lab->floor,
                    'room' => $lab->room,
                    'department' => $lab->department,
                    'campus' => $lab->campus,
                    'phone' => $lab->phone,
                    'contact_email' => $lab->contact_email,
                    'website' => $lab->website,
                    'working_hours' => $lab->working_hours,
                    'has_accessibility' => $lab->has_accessibility
                ]
            ];
        });

        Log::info('Dados formatados para o mapa: ' . $formattedLabs->count() . ' laboratórios');

        return view('labs.map', compact('formattedLabs'));
    }

    /**
     * Retorna os dados dos laboratórios em formato JSON para uso com API
     */
    public function getLabsData()
    {
        $labs = Team::whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->where('latitude', '!=', 0)
            ->where('longitude', '!=', 0)
            ->get()
            ->map(function ($lab) {
                return [
                    'id' => $lab->id,
                    'name' => $lab->name,
                    'address' => $lab->address,
                    'formatted_address' => $lab->formatted_address ?? $lab->address,
                    'coordinates' => [
                        'lat' => (float) $lab->latitude,
                        'lng' => (float) $lab->longitude
                    ],
                    'details' => [
                        'building' => $lab->building,
                        'floor' => $lab->floor,
                        'room' => $lab->room,
                        'department' => $lab->department,
                        'campus' => $lab->campus,
                        'phone' => $lab->phone,
                        'contact_email' => $lab->contact_email,
                        'website' => $lab->website,
                        'working_hours' => $lab->working_hours,
                        'has_accessibility' => $lab->has_accessibility
                    ]
                ];
            });

        return response()->json($labs);
    }
}
