<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Country;
use App\Models\State;
use App\Models\Municipality;
use App\Models\Institution;
use Illuminate\Support\Facades\File;

class ImportData extends Command
{
    protected $signature = 'import:data';
    protected $description = 'Importa dados de países, estados, municípios e instituições';

    public function handle()
    {
        $this->importCountries();
        $this->importStatesAndMunicipalities();
        $this->importInstitutions();
        $this->info('Dados importados com sucesso!');
    }

    private function importCountries()
    {
        $filePath = storage_path('app/data/countries.txt');
        if (!File::exists($filePath)) {
            $this->error("Arquivo $filePath não encontrado!");
            return;
        }

        $lines = file($filePath, FILE_IGNORE_NEW_LINES);
        array_shift($lines); // Remover cabeçalho

        foreach ($lines as $line) {
            $data = preg_split('/\s{2,}/', $line); // Divide com espaços múltiplos
            if (count($data) === 2) {
                Country::updateOrCreate(['code' => $data[0]], ['name' => $data[1]]);
            }
        }

        $this->info("Países importados!");
    }

    private function importStatesAndMunicipalities()
    {
        $filePath = storage_path('app/data/municipios.txt');
        if (!File::exists($filePath)) {
            $this->error("Arquivo $filePath não encontrado!");
            return;
        }

        $lines = file($filePath, FILE_IGNORE_NEW_LINES);
        array_shift($lines);

        foreach ($lines as $line) {
            $parts = explode("\t", $line);
            if (count($parts) >= 5) {
                $state = State::firstOrCreate(['name' => $parts[4]]);
                Municipality::firstOrCreate(['name' => $parts[3], 'state_id' => $state->id]);
            }
        }

        $this->info("Estados e Municípios importados!");
    }

    private function importInstitutions()
    {
        $filePath = storage_path('app/data/institutions.txt');
        if (!File::exists($filePath)) {
            $this->error("Arquivo $filePath não encontrado!");
            return;
        }

        $brazil = Country::firstOrCreate(['code' => 'BR'], ['name' => 'Brazil']);
        $lines = file($filePath, FILE_IGNORE_NEW_LINES);
        array_shift($lines);

        foreach ($lines as $line) {
            $parts = explode("\t", $line);
            if (count($parts) >= 3) {
                $state = State::firstOrCreate(['name' => $parts[2]]);
                // Fetch a municipality for this state (e.g., first available)
                $municipality = Municipality::where('state_id', $state->id)->first();
                if (!$municipality) {
                    $this->warn("Nenhum município encontrado para o estado {$state->name}. Pulando instituição {$parts[0]}.");
                    continue;
                }
                Institution::firstOrCreate([
                    'name' => $parts[0],
                    'state_id' => $state->id,
                    'municipality_id' => $municipality->id,
                    'country_code' => $brazil->code
                ]);
            }
        }

        $this->info("Instituições importadas!");
    }
}
