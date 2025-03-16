<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateEquipmentsTableAddServiceAndCollaborationColumns extends Migration
{
    public function up()
    {
        Schema::table('equipments', function (Blueprint $table) {
            // Remover o campo antigo
            $table->dropColumn('available_for_service');
            // Adicionar os novos campos
            $table->boolean('available_for_services')->default(false);
            $table->boolean('available_for_collaboration')->default(false);
        });
    }

    public function down()
    {
        Schema::table('equipments', function (Blueprint $table) {
            // Reverter: adicionar o campo antigo e remover os novos
            $table->boolean('available_for_service')->default(false);
            $table->dropColumn('available_for_services');
            $table->dropColumn('available_for_collaboration');
        });
    }
}
