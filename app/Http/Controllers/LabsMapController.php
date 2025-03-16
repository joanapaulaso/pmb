<?php

namespace App\Http\Controllers;

use App\Models\Team;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class LabsMapController extends Controller
{
    /**
     * Exibe a página do mapa de laboratórios
     */
    public function index()
    {
        Log::info('Iniciando busca de laboratórios para o mapa');

        $totalLabs = Team::count();
        Log::info('Total de laboratórios no sistema: ' . $totalLabs);

        $labsWithCoordinates = Team::whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->count();
        Log::info('Laboratórios com alguma coordenada: ' . $labsWithCoordinates);

        $labsWithValidCoords = Team::whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->where('latitude', '!=', 0)
            ->where('longitude', '!=', 0)
            ->count();
        Log::info('Laboratórios com coordenadas válidas (não-zero): ' . $labsWithValidCoords);

        $labs = Team::whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->where('latitude', '!=', 0)
            ->where('longitude', '!=', 0)
            ->get();

        Log::info('Laboratórios encontrados: ' . $labs->count());

        foreach ($labs as $lab) {
            Log::info("Lab {$lab->id} - {$lab->name}: lat={$lab->latitude}, lng={$lab->longitude}");
        }

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

    public function show(Team $team, Request $request)
    {
        Log::info('Exibindo perfil público do laboratório', ['team_id' => $team->id]);

        $lab = $team->load(['users', 'equipments']); // Carrega os equipamentos junto com os usuários

        $tagColors = config('tags.colors', []);
        $tags = array_keys($tagColors);

        $query = Post::whereNull('parent_id')
            ->whereIn('user_id', $lab->users->pluck('id'))
            ->with(['user', 'replies.user'])
            ->latest();

        $selectedTags = $request->input('tags', []);
        if (!is_array($selectedTags)) {
            $selectedTags = array_filter(explode(',', $selectedTags));
        }
        $selectedTags = array_slice($selectedTags, 0, 3);

        if (!empty($selectedTags) && !in_array('all', $selectedTags)) {
            $query->where(function ($q) use ($selectedTags) {
                $q->whereIn('tag', $selectedTags)
                    ->orWhereJsonContains('additional_tags', $selectedTags);
            });
        }

        $labFilter = $request->input('lab_filter', 'false') === 'true';
        if ($labFilter) {
            $query->where('is_lab_publication', true);
        }

        $posts = $query->paginate(20);
        $posts->appends(['tags' => implode(',', $selectedTags), 'lab_filter' => $labFilter ? 'true' : 'false']);

        $labData = [
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
            ],
            'equipments' => $lab->equipments->map(function ($equipment) {
                return [
                    'id' => $equipment->id,
                    'model' => $equipment->model,
                    'brand' => $equipment->brand,
                    'technical_responsible' => $equipment->technical_responsible,
                    'available_for_services' => $equipment->available_for_services,
                    'available_for_collaboration' => $equipment->available_for_collaboration,
                    'photo_path' => $equipment->photo_path ? Storage::url($equipment->photo_path) : null,
                ];
            })->toArray()
        ];

        Log::info('Dados do laboratório carregados', ['lab' => $labData]);
        Log::info('Posts encontrados', ['count' => $posts->total()]);

        return view('labs.show', compact('labData', 'posts', 'tags', 'selectedTags', 'tagColors'));
    }
}
