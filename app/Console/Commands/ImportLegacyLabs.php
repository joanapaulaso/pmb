<?php

namespace App\Console\Commands;

use App\Models\Team;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class ImportLegacyLabs extends Command
{
    protected $signature = 'labs:import-legacy {path=storage/app/data/old_labs_map.txt}';

    protected $description = 'Importa laboratórios legados do arquivo TSV (id, nome, pesquisadores, tecnicas_analiticas, linhas_de_pesquisa, website, endereco)';

    public function handle(): int
    {
        $path = base_path($this->argument('path'));

        if (!file_exists($path)) {
            $this->error("Arquivo não encontrado: {$path}");
            return self::FAILURE;
        }

        $owner = User::where('admin', true)->first() ?? User::first();
        if (!$owner) {
            $this->error('Nenhum usuário encontrado para ser dono dos times importados.');
            return self::FAILURE;
        }

        $handle = fopen($path, 'r');
        if (!$handle) {
            $this->error('Não foi possível abrir o arquivo.');
            return self::FAILURE;
        }

        $header = fgetcsv($handle, 0, "\t"); // consume header
        $imported = 0;

        while (($row = fgetcsv($handle, 0, "\t")) !== false) {
            if (count($row) < 7) {
                continue;
            }

            [$legacyId, $name, $researchers, $techniques, $researchLines, $website, $address] = $row;

            $data = [
                'user_id' => $owner->id,
                'name' => $name,
                'personal_team' => false,
                'description' => null,
                'address' => $address,
                'website' => $website ?: null,
                'researchers' => $researchers ?: null,
                'analytical_techniques' => $techniques ?: null,
                'research_lines' => $researchLines ?: null,
                'is_legacy' => true,
                'is_claimed' => false,
            ];

            $team = Team::updateOrCreate(
                ['legacy_source_id' => $legacyId],
                $data
            );

            $imported++;
        }

        fclose($handle);

        $this->info("Importação concluída: {$imported} laboratórios processados.");
        Log::info('Importação de laboratórios legados concluída', ['count' => $imported]);

        return self::SUCCESS;
    }
}
