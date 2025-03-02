<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('user_categories', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('category_id'); // Referência à categoria/subcategoria
            $table->string('category_name'); // Nome da categoria principal
            $table->string('subcategory_name'); // Nome da subcategoria
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')
                ->onDelete('cascade');
            $table->foreign('category_id')->references('id')->on('categories')
                ->onDelete('cascade');

            // Evitar duplicatas
            $table->unique(['user_id', 'category_id', 'subcategory_name']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('user_categories');
    }
};
