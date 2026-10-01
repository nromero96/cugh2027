<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPanelReviewInstructionsSentAtToReviewerCandidates extends Migration
{
    public function up()
    {
        Schema::table('reviewer_candidates', function (Blueprint $table) {
            $table->timestamp('panel_review_instructions_sent_at')->nullable()->after('review_instructions_sent_at');
        });
    }

    public function down()
    {
        Schema::table('reviewer_candidates', function (Blueprint $table) {
            $table->dropColumn('panel_review_instructions_sent_at');
        });
    }
}
