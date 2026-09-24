<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddReviewInstructionsSentAtToReviewerCandidates extends Migration
{
    public function up()
    {
        Schema::table('reviewer_candidates', function (Blueprint $table) {
            $table->timestamp('review_instructions_sent_at')->nullable()->after('imported_at');
        });
    }

    public function down()
    {
        Schema::table('reviewer_candidates', function (Blueprint $table) {
            $table->dropColumn('review_instructions_sent_at');
        });
    }
}
