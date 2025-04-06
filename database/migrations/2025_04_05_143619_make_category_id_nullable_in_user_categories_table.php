<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('user_categories', function (Blueprint $table) {
            // Remove a restrição de chave estrangeira temporariamente
            $table->dropForeign(['category_id']);
            // Torna a coluna category_id nullable
            $table->unsignedBigInteger('category_id')->nullable()->change();
            // Re-adiciona a restrição de chave estrangeira
            $table->foreign('category_id')->references('id')->on('categories')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::table('user_categories', function (Blueprint $table) {
            // Reverte a mudança, tornando category_id NOT NULL novamente
            $table->dropForeign(['category_id']);
            $table->unsignedBigInteger('category_id')->nullable(false)->change();
            $table->foreign('category_id')->references('id')->on('categories')->onDelete('cascade');
        });
    }
};