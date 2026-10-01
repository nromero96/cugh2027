<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePanelReviewersTable extends Migration
{
    public function up()
    {
        Schema::create('panel_reviewers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('panel_id')->constrained('panels')->onDelete('cascade');
            $table->foreignId('reviewer_id')->constrained('users')->onDelete('cascade');
            for ($number = 1; $number <= 8; $number++) {
                $table->unsignedTinyInteger('score_'.$number)->nullable();
            }
            $table->decimal('average_score', 4, 2)->nullable();
            $table->text('reviewer_note')->nullable();
            $table->timestamps();

            $table->unique(['panel_id', 'reviewer_id']);
            $table->index('reviewer_id');
        });
    }

    public function down()
    {
        Schema::dropIfExists('panel_reviewers');
    }
}
