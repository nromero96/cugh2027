<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCouponRulesToSpecialCodes extends Migration
{
    public function up()
    {
        Schema::table('special_codes', function (Blueprint $table) {
            $table->string('usage_rule', 20)->default('limited')->after('quantity');
            $table->string('allowed_email')->nullable()->after('usage_rule');
        });

        Schema::table('inscriptions', function (Blueprint $table) {
            $table->string('special_code_email')->nullable()->after('special_code');
        });

        Schema::create('special_code_categories', function (Blueprint $table) {
            $table->unsignedBigInteger('special_code_id');
            $table->unsignedBigInteger('category_inscription_id');
            $table->primary(['special_code_id', 'category_inscription_id'], 'special_code_category_pk');
            $table->foreign('special_code_id')->references('id')->on('special_codes')->onDelete('cascade');
            $table->foreign('category_inscription_id')->references('id')->on('category_inscriptions')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('special_code_categories');
        Schema::table('inscriptions', function (Blueprint $table) {
            $table->dropColumn('special_code_email');
        });
        Schema::table('special_codes', function (Blueprint $table) {
            $table->dropColumn(['usage_rule', 'allowed_email']);
        });
    }
}
