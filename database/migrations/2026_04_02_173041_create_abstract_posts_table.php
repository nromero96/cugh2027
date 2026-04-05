<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAbstractPostsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('abstract_posts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('presentation_type')->nullable();
            $table->string('title',250)->nullable();
            $table->text('co_authors')->nullable();
            $table->text('institutions')->nullable();
            $table->string('abstract_type')->nullable();
            $table->string('subtopic')->nullable();
            $table->text('body')->nullable();
            $table->text('keywords')->nullable();
            $table->string('status',50)->default('draft');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('abstract_posts');
    }
}
