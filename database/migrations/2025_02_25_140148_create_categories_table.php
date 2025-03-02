<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('type')->nullable(); // 'category' ou 'subcategory'
            $table->unsignedBigInteger('parent_id')->nullable(); // Para subcategorias
            $table->timestamps();

            $table->foreign('parent_id')->references('id')->on('categories')
                ->onDelete('cascade');

            // Índice para buscas mais rápidas
            $table->index(['type', 'parent_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('categories');
    }
};
