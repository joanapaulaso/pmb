<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePortalPostsTable extends Migration
{
    public function up()
    {
        Schema::create('portal_posts', function (Blueprint $table) {
            $table->id();
            $table->text('content');
            $table->string('media')->nullable();
            $table->string('media_type')->nullable();
            $table->boolean('pinned')->default(false);
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('portal_posts');
    }
}
