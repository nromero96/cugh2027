<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAbstractPostNotesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('abstract_post_notes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('abstract_post_id');
            $table->unsignedBigInteger('user_id');

            $table->text('comment');

            $table->string('status_change')->nullable();

            $table->timestamps();

            $table->foreign('abstract_post_id')
                ->references('id')
                ->on('abstract_posts')
                ->onDelete('cascade');

            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('abstract_post_notes');
    }
}
