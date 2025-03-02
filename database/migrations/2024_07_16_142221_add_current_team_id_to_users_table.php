<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// database/migrations/2024_07_16_142221_add_current_team_id_to_users_table.php

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Schema::table('users', function (Blueprint $table) {
        //     $table->foreignId('current_team_id')->nullable()->after('id');
        // });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Schema::table('users', function (Blueprint $table) {
        //     $table->dropColumn('current_team_id');
        // });
    }
};

