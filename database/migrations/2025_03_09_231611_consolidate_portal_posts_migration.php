<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Certifique-se de que não existe para evitar erros
        if (Schema::hasTable('portal_posts')) {
            // Verificar cada migração antes de inserir
            foreach (
                [
                    '2025_03_08_164600_create_portal_posts_table',
                    '2025_03_09_201131_alter_portal_posts_table',
                    '2025_03_09_211029_rename_portal_posts_table_to_post_portals_table',
                    '2025_03_09_211029_re_rename_portal_posts_table_to_post_portals_table',
                    '2025_03_09_215938_add_additional_tags_to_portal_posts_table'
                ] as $migration
            ) {
                if (!DB::table('migrations')->where('migration', $migration)->exists()) {
                    DB::table('migrations')->insert([
                        'migration' => $migration,
                        'batch' => DB::table('migrations')->max('batch') ?: 1
                    ]);
                }
            }
            return; // Tabela já existe, não precisamos criar
        }

        // Criar a tabela com a estrutura final correta
        Schema::create('portal_posts', function (Blueprint $table) {
            $table->id();
            $table->text('content');
            $table->string('tag')->default('general');
            $table->json('additional_tags')->nullable();
            $table->json('metadata')->nullable();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->unsignedBigInteger('parent_id')->nullable();
            $table->timestamps();

            // Adicionar chave estrangeira para parent_id
            $table->foreign('parent_id')->references('id')->on('portal_posts')->onDelete('cascade');
        });

        // Marcar todas as migrações anteriores relacionadas como executadas
        DB::table('migrations')->insert([
            ['migration' => '2025_03_08_164600_create_portal_posts_table', 'batch' => 1],
            ['migration' => '2025_03_09_201131_alter_portal_posts_table', 'batch' => 1],
            ['migration' => '2025_03_09_211029_rename_portal_posts_table_to_post_portals_table', 'batch' => 1],
            ['migration' => '2025_03_09_211029_re_rename_portal_posts_table_to_post_portals_table', 'batch' => 1],
            ['migration' => '2025_03_09_215938_add_additional_tags_to_portal_posts_table', 'batch' => 1]
        ]);
    }

    public function down()
    {
        Schema::dropIfExists('portal_posts');
    }
};
