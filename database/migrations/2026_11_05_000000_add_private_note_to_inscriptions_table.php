<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPrivateNoteToInscriptionsTable extends Migration
{
    public function up()
    {
        Schema::table('inscriptions', function (Blueprint $table) {
            $table->text('private_note')->nullable();
        });
    }

    public function down()
    {
        Schema::table('inscriptions', function (Blueprint $table) {
            $table->dropColumn('private_note');
        });
    }
}
