<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('laboratories', function (Blueprint $table) {
            // Adiciona os campos que ainda não existem
            $table->string('building')->nullable()->after('logo');
            $table->string('floor')->nullable()->after('building');
            $table->string('room')->nullable()->after('floor');
            $table->string('department')->nullable()->after('room');
            $table->string('campus')->nullable()->after('department');
            $table->string('phone')->nullable()->after('campus');
            $table->string('contact_email')->nullable()->after('phone');
            $table->string('working_hours')->nullable()->after('contact_email');
            $table->boolean('has_accessibility')->default(false)->after('working_hours');
        });
    }

    public function down()
    {
        Schema::table('laboratories', function (Blueprint $table) {
            // Remove os campos adicionados no rollback
            $table->dropColumn([
                'description',
                'website',
                'address',
                'lat',
                'lng',
                'logo',
            ]);
        });
    }
};
