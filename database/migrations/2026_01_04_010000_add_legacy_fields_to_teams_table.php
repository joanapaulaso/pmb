<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('teams', function (Blueprint $table) {
            $table->string('legacy_source_id')->nullable()->after('id');
            $table->boolean('is_legacy')->default(false)->after('personal_team');
            $table->boolean('is_claimed')->default(true)->after('is_legacy');
            $table->text('researchers')->nullable()->after('description');
            $table->text('analytical_techniques')->nullable()->after('researchers');
            $table->text('research_lines')->nullable()->after('analytical_techniques');
        });
    }

    public function down(): void
    {
        Schema::table('teams', function (Blueprint $table) {
            $table->dropColumn([
                'legacy_source_id',
                'is_legacy',
                'is_claimed',
                'researchers',
                'analytical_techniques',
                'research_lines',
            ]);
        });
    }
};
