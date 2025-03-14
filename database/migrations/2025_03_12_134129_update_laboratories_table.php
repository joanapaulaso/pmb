<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('laboratories', function (Blueprint $table) {
            // Adicionar colunas adicionais para mais informações sobre os laboratórios
            if (!Schema::hasColumn('laboratories', 'description')) {
                $table->text('description')->nullable();
            }

            if (!Schema::hasColumn('laboratories', 'website')) {
                $table->string('website')->nullable();
            }

            if (!Schema::hasColumn('laboratories', 'address')) {
                $table->string('address')->nullable();
            }

            if (!Schema::hasColumn('laboratories', 'lat')) {
                $table->decimal('lat', 10, 8)->nullable();
            }

            if (!Schema::hasColumn('laboratories', 'lng')) {
                $table->decimal('lng', 11, 8)->nullable();
            }

            if (!Schema::hasColumn('laboratories', 'logo')) {
                $table->string('logo')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('laboratories', function (Blueprint $table) {
            $table->dropColumn([
                'description',
                'website',
                'address',
                'lat',
                'lng',
                'logo'
            ]);
        });
    }
};
