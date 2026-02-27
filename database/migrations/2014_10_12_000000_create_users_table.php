<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('email')->unique();
            $table->string('cc_email',100)->nullable();
            $table->string('salutation',30)->nullable();
            $table->string('name');
            $table->string('lastname')->nullable();
            $table->string('second_lastname')->nullable();
            $table->string('degrees')->nullable();
            $table->string('other_degrees')->nullable();
            $table->boolean('is_cugh_member')->default(false);
            $table->string('cugh_member_institution')->nullable();
            $table->string('job_title')->nullable();
            $table->string('document_type',50)->nullable();
            $table->string('document_number',50)->unique();
            $table->unsignedBigInteger('nationality')->nullable();
            $table->foreign('nationality')->references('id')->on('countries')->onDelete('restrict');
            $table->string('gender',20)->nullable();
            $table->string('occupation')->nullable();
            $table->string('occupation_other')->nullable();
            $table->string('workplace')->nullable();
            $table->string('address')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->unsignedBigInteger('country')->nullable();
            $table->foreign('country')->references('id')->on('countries')->onDelete('restrict');
            $table->string('work_phone_code',10)->nullable();
            $table->string('work_phone_code_city',10)->nullable();
            $table->string('work_phone_number',30)->nullable();
            $table->string('phone_code',10)->nullable();
            $table->string('phone_number',30)->nullable();
            $table->string('whatsapp_code',10)->nullable();
            $table->string('whatsapp_number',30)->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();
            $table->string('status',30);
            $table->text('photo');
            $table->string('solapin_name',70)->nullable();
            $table->string('solapin_lastname',70)->nullable();

            //Questionnaire data
            $table->text('sector')->nullable();
            $table->string('other_sector')->nullable();

            $table->text('area_of_work')->nullable();
            $table->string('other_area_of_work')->nullable();

            $table->text('how_did_you_hear_about')->nullable();
            $table->string('other_how_did_you_hear_about')->nullable();

            $table->text('why_attending')->nullable();
            $table->string('other_why_attending')->nullable();

            $table->string('ability_to_present_work')->nullable();

            $table->text('how_is_your_attendance_funded')->nullable();
            $table->string('other_how_is_your_attendance_funded')->nullable();

            $table->text('your_areas_of_focus_in_global_health')->nullable();
            $table->string('other_your_areas_of_focus_in_global_health')->nullable();

            $table->text('obstacles_to_attending_cughs_conferences')->nullable();
            $table->string('other_obstacles_to_attending_cughs_conferences')->nullable();

            $table->string('receive_news_and_updates')->nullable();
            $table->string('contact_info')->nullable();
            $table->string('oral_poster_abstract_presenter')->nullable();
            $table->string('panel_presenter_moderator')->nullable();

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
        Schema::dropIfExists('users');
    }
}
