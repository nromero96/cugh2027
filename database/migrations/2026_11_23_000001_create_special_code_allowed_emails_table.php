<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSpecialCodeAllowedEmailsTable extends Migration
{
    public function up()
    {
        Schema::create('special_code_allowed_emails', function (Blueprint $table) {
            $table->id();
            $table->foreignId('special_code_id')->constrained('special_codes')->cascadeOnDelete();
            $table->string('email');
        });
    }

    public function down()
    {
        Schema::dropIfExists('special_code_allowed_emails');
    }
}
