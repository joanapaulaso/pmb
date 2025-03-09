<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterPortalPostsTable extends Migration
{
    public function up()
    {
        Schema::table('portal_posts', function (Blueprint $table) {
            // Adicionar novas colunas
            $table->unsignedBigInteger('parent_id')->nullable()->after('user_id');
            $table->string('tag')->default('general')->after('content');
            $table->json('metadata')->nullable()->after('tag');

            // Remover colunas que não são mais necessárias
            $table->dropColumn(['media', 'media_type', 'pinned']);

            // Adicionar chave estrangeira para parent_id
            $table->foreign('parent_id')->references('id')->on('portal_posts')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::table('portal_posts', function (Blueprint $table) {
            // Reverter as alterações

            // Remover a chave estrangeira
            $table->dropForeign(['parent_id']);

            // Remover as novas colunas
            $table->dropColumn(['parent_id', 'tag', 'metadata']);

            // Restaurar as colunas removidas
            $table->string('media')->nullable();
            $table->string('media_type')->nullable();
            $table->boolean('pinned')->default(false);
        });
    }
}
