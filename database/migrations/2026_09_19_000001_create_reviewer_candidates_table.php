<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReviewerCandidatesTable extends Migration
{
    public function up()
    {
        Schema::create('reviewer_candidates', function (Blueprint $table) {
            $table->id();
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('salutation')->nullable();
            $table->text('professional_position_academic_title')->nullable();
            $table->string('institution')->nullable();
            $table->string('country')->nullable();
            $table->string('email')->unique();
            $table->text('review_interest')->nullable();
            $table->text('panel_languages')->nullable();
            $table->text('subthemes')->nullable();
            $table->string('source_file')->nullable();
            $table->unsignedInteger('source_row')->nullable();
            $table->timestamp('imported_at')->nullable();
            $table->timestamps();

            $table->index(['country', 'institution']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('reviewer_candidates');
    }
}
