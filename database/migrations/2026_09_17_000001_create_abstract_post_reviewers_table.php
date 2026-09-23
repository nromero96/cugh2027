<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAbstractPostReviewersTable extends Migration
{
    public function up()
    {
        Schema::create('abstract_post_reviewers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('abstract_post_id')->constrained('abstract_posts')->onDelete('cascade');
            $table->foreignId('reviewer_id')->constrained('users')->onDelete('cascade');
            $table->unsignedTinyInteger('score_1')->nullable();
            $table->unsignedTinyInteger('score_2')->nullable();
            $table->unsignedTinyInteger('score_3')->nullable();
            $table->unsignedTinyInteger('score_4')->nullable();
            $table->unsignedTinyInteger('score_5')->nullable();
            $table->decimal('average_score', 4, 2)->nullable();
            $table->text('reviewer_note')->nullable();
            $table->timestamps();

            $table->unique(['abstract_post_id', 'reviewer_id'], 'abstract_reviewer_unique');
            $table->index('reviewer_id');
        });
    }

    public function down()
    {
        Schema::dropIfExists('abstract_post_reviewers');
    }
}
