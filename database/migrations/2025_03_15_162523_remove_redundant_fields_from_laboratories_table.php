<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('laboratories', function (Blueprint $table) {
            // Remove os campos que agora estão centralizados em teams
            $table->dropColumn([
                'description',
                'website',
                'address',
                'lat',
                'lng',
                'logo',
                'building',
                'floor',
                'room',
                'department',
                'campus',
                'phone',
                'contact_email',
                'working_hours',
                'has_accessibility'
            ]);
        });
    }

    public function down()
    {
        Schema::table('laboratories', function (Blueprint $table) {
            // Re-adiciona os campos no rollback, caso necessário
            $table->text('description')->nullable()->after('team_id');
            $table->string('website')->nullable()->after('description');
            $table->string('address')->nullable()->after('website');
            $table->decimal('lat', 10, 7)->nullable()->after('address');
            $table->decimal('lng', 10, 7)->nullable()->after('lat');
            $table->string('logo')->nullable()->after('lng');
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
};
