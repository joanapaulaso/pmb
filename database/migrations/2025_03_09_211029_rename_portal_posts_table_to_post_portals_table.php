<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RenamePortalPostsTableToPostPortalsTable extends Migration
{
    public function up()
    {
        // No action needed; table is already 'portal_posts'
    }

    public function down()
    {
        // No action needed
    }
}
