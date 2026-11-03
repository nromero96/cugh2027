<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class RenameAbstractNotSelectedStatus extends Migration
{
    public function up()
    {
        DB::table('abstract_posts')
            ->where('status', 'not_selected')
            ->update(['status' => 'not_accepted']);
    }

    public function down()
    {
        DB::table('abstract_posts')
            ->where('status', 'not_accepted')
            ->update(['status' => 'not_selected']);
    }
}
