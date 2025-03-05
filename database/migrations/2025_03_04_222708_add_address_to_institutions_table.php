<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAddressToInstitutionsTable extends Migration
{
    public function up()
    {
        Schema::table('institutions', function (Blueprint $table) {
            $table->string('address')->nullable()->after('name');
        });
    }

    public function down()
    {
        Schema::table('institutions', function (Blueprint $table) {
            $table->dropColumn('address');
        });
    }
}
