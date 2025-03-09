<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ReRenamePortalPostsTableToPostPortalsTable extends Migration
{

    public function up()
        {
            Schema::rename('post_portals', 'portal_posts');
        }

    public function down()
    {
        Schema::rename('portal_posts', 'post_portals');
    }
}
