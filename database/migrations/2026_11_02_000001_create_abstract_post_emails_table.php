<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAbstractPostEmailsTable extends Migration
{
    public function up()
    {
        Schema::create('abstract_post_emails', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('abstract_post_id')->index();
            $table->unsignedBigInteger('sent_by_user_id')->nullable();
            $table->string('kind', 40);
            $table->string('recipient_email');
            $table->string('from_email')->nullable();
            $table->string('reply_to_email')->nullable();
            $table->string('subject');
            $table->mediumText('body_html');
            $table->string('attachment_disk', 40)->nullable();
            $table->string('attachment_path')->nullable();
            $table->string('attachment_filename')->nullable();
            $table->string('attachment_mime', 100)->nullable();
            $table->string('status', 20)->default('pending');
            $table->timestamp('sent_at')->nullable();
            $table->text('error_message')->nullable();
            $table->timestamps();

            $table->index(['abstract_post_id', 'kind', 'status']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('abstract_post_emails');
    }
}
