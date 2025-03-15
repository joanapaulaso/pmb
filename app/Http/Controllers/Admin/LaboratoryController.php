<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Laboratory;
use App\Models\Institution;
use App\Models\State;
use App\Models\Team;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class LaboratoryController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['auth', 'admin']);
    }

    /**
     * Display a listing of the laboratories.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $query = Laboratory::with(['institution', 'state', 'team']);

        // Filtrar por presença de team_id
        $hasTeam = $request->input('has_team', '1'); // Por padrão, mostra apenas labs com team_id

        if ($hasTeam === '1') {
            $query->whereNotNull('team_id');
        } elseif ($hasTeam === '0') {
            $query->whereNull('team_id');
        }
        // Se for 'all' (ou qualquer outro valor), não aplica nenhum filtro

        $laboratories = $query->latest()->paginate(15);

        return view('admin.laboratories.index', [
            'laboratories' => $laboratories,
            'hasTeam' => $hasTeam
        ]);
    }

    /**
     * Show the form for creating a new laboratory.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        // No longer need to pass institutions and states since they're loaded via Livewire
        return view('admin.laboratories.create');
    }

    /**
     * Store a newly created laboratory in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'institution_id' => 'required|exists:institutions,id',
            'state_id' => 'required|exists:states,id',
            'description' => 'nullable|string',
            'website' => 'nullable|url',
            'address' => 'nullable|string',
            'lat' => 'nullable|numeric',
            'lng' => 'nullable|numeric',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'building' => 'nullable|string',
            'floor' => 'nullable|string',
            'room' => 'nullable|string',
            'department' => 'nullable|string',
            'campus' => 'nullable|string',
            'phone' => 'nullable|string',
            'contact_email' => 'nullable|email',
            'working_hours' => 'nullable|string',
            'has_accessibility' => 'boolean',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        DB::beginTransaction();
        try {
            // Criar a equipe (team) com os dados adicionais
            $team = new Team();
            $team->name = $request->name;
            $team->personal_team = false;
            $team->user_id = auth()->id();
            $team->description = $request->description;
            $team->website = $request->website;
            $team->address = $request->address;
            $team->latitude = $request->lat;
            $team->longitude = $request->lng;
            $team->building = $request->building;
            $team->floor = $request->floor;
            $team->room = $request->room;
            $team->department = $request->department;
            $team->campus = $request->campus;
            $team->phone = $request->phone;
            $team->contact_email = $request->contact_email;
            $team->working_hours = $request->working_hours;
            $team->has_accessibility = $request->has_accessibility ?? false;

            if ($request->hasFile('logo')) {
                $path = $request->file('logo')->store('laboratories', 'public');
                $team->logo = $path;
            }

            $team->save();

            // Criar o laboratório vinculado à equipe
            $laboratory = new Laboratory();
            $laboratory->name = $request->name;
            $laboratory->institution_id = $request->institution_id;
            $laboratory->state_id = $request->state_id;
            $laboratory->team_id = $team->id;
            $laboratory->save();

            DB::commit();
            return redirect()->route('admin.laboratories.index')->with('success', 'Laboratory created successfully');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Error creating laboratory: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Show the form for editing the specified laboratory.
     *
     * @param  \App\Models\Laboratory  $laboratory
     * @return \Illuminate\View\View
     */
    /**
     * Show the form for editing the specified laboratory.
     *
     * @param  \App\Models\Laboratory  $laboratory
     * @return \Illuminate\View\View
     */
    public function edit(Laboratory $laboratory)
    {
        // Carregar a relação 'team' junto com o laboratório
        $laboratory->load('team');
        return view('admin.laboratories.edit', compact('laboratory'));
    }

    /**
     * Update the specified laboratory in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Laboratory  $laboratory
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Laboratory $laboratory)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'institution_id' => 'required|exists:institutions,id',
            'state_id' => 'required|exists:states,id',
            'description' => 'nullable|string',
            'website' => 'nullable|url',
            'address' => 'nullable|string',
            'lat' => 'nullable|numeric',
            'lng' => 'nullable|numeric',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'building' => 'nullable|string',
            'floor' => 'nullable|string',
            'room' => 'nullable|string',
            'department' => 'nullable|string',
            'campus' => 'nullable|string',
            'phone' => 'nullable|string',
            'contact_email' => 'nullable|email',
            'working_hours' => 'nullable|string',
            'has_accessibility' => 'boolean',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        DB::beginTransaction();
        try {
            // Atualizar o laboratório
            $laboratory->name = $request->name;
            $laboratory->institution_id = $request->institution_id;
            $laboratory->state_id = $request->state_id;
            $laboratory->save();

            // Atualizar a equipe associada, se existir
            if ($laboratory->team) {
                $team = $laboratory->team;
                $team->name = $request->name;
                $team->description = $request->description;
                $team->website = $request->website;
                $team->address = $request->address;
                $team->latitude = $request->lat;
                $team->longitude = $request->lng;
                $team->building = $request->building;
                $team->floor = $request->floor;
                $team->room = $request->room;
                $team->department = $request->department;
                $team->campus = $request->campus;
                $team->phone = $request->phone;
                $team->contact_email = $request->contact_email;
                $team->working_hours = $request->working_hours;
                $team->has_accessibility = $request->has_accessibility ?? false;

                if ($request->hasFile('logo')) {
                    $path = $request->file('logo')->store('laboratories', 'public');
                    $team->logo = $path;
                }

                $team->save();
            }

            DB::commit();
            return redirect()->route('admin.laboratories.index')->with('success', 'Laboratory updated successfully');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Error updating laboratory: ' . $e->getMessage())->withInput();
        }
    }


    /**
     * Remove the specified laboratory from storage.
     *
     * @param  \App\Models\Laboratory  $laboratory
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Laboratory $laboratory)
    {
        DB::beginTransaction();

        try {
            // Delete the team if exists
            if ($laboratory->team_id) {
                $team = Team::find($laboratory->team_id);
                if ($team) {
                    $team->delete();
                }
            }

            // Delete the laboratory
            $laboratory->delete();

            DB::commit();

            return redirect()->route('admin.laboratories.index')
                ->with('success', 'Laboratory deleted successfully');
        } catch (\Exception $e) {
            DB::rollback();

            return redirect()->back()
                ->with('error', 'Error deleting laboratory: ' . $e->getMessage());
        }
    }
}
