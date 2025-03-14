<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddUniqueIndexToLaboratoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('laboratories', function (Blueprint $table) {
            // Remove registros duplicados antes de adicionar o índice único
            $this->removeDuplicates();

            // Adiciona um índice único para a combinação de nome, instituição, estado e time
            $table->unique(['name', 'institution_id', 'state_id', 'team_id'], 'laboratories_unique_index');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('laboratories', function (Blueprint $table) {
            $table->dropIndex('laboratories_unique_index');
        });
    }

    /**
     * Remove laboratórios duplicados mantendo os que têm team_id
     *
     * @return void
     */
    private function removeDuplicates()
    {
        // Identifica grupos de laboratórios com os mesmos name, institution_id e state_id
        $duplicates = DB::table('laboratories')
            ->select('name', 'institution_id', 'state_id', DB::raw('COUNT(*) as count'))
            ->groupBy('name', 'institution_id', 'state_id')
            ->having('count', '>', 1)
            ->get();

        foreach ($duplicates as $duplicate) {
            // Para cada grupo de duplicatas, encontra todos os registros
            $labs = DB::table('laboratories')
                ->where('name', $duplicate->name)
                ->where('institution_id', $duplicate->institution_id)
                ->where('state_id', $duplicate->state_id)
                ->orderBy('team_id', 'desc') // Prioriza laboratórios com team_id (não nulos primeiro)
                ->get();

            // O primeiro registro será mantido (preferencialmente o que tem team_id)
            $keepId = $labs->first()->id;

            // Atualiza referências nos perfis dos laboratórios que serão excluídos
            foreach ($labs as $lab) {
                if ($lab->id != $keepId) {
                    DB::table('profiles')
                        ->where('laboratory_id', $lab->id)
                        ->update(['laboratory_id' => $keepId]);

                    // Exclui o laboratório duplicado
                    DB::table('laboratories')
                        ->where('id', $lab->id)
                        ->delete();
                }
            }
        }
    }
}
