<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('portal_posts', function (Blueprint $table) {
            // Adicione a coluna JSON para additional_tags
            $table->json('additional_tags')->nullable()->after('tag');
        });
    }

    public function down()
    {
        Schema::table('portal_posts', function (Blueprint $table) {
            $table->dropColumn('additional_tags');
        });
    }
};
