<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePendingLabCoordinatorsTable extends Migration
{
    public function up()
    {
        Schema::create('pending_lab_coordinators', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('laboratory_id')->constrained()->onDelete('cascade');
            $table->string('token')->unique(); // Authorization token
            $table->boolean('approved')->default(false);
            $table->timestamp('expires_at')->nullable(); // Token expiration
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('pending_lab_coordinators');
    }
}