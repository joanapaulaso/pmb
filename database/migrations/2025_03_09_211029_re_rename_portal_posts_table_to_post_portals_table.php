<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ReRenamePortalPostsTableToPostPortalsTable extends Migration
{
    public function up()
    {
        // Do nothing since the table is already correctly named 'portal_posts'
    }

    public function down()
    {
        // Do nothing
    }
}
