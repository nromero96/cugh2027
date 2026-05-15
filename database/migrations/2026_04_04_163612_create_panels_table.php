<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePanelsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('panels', function (Blueprint $table) {
            $table->id();
            
            // PANEL
            $table->string('language')->nullable();
            $table->json('subthemes')->nullable();
            $table->text('subthemes_other')->nullable();
            $table->string('title', 150)->nullable();

            // CONTACT PERSON
            $table->string('contact_salutation')->nullable();
            $table->string('contact_name')->nullable();
            $table->string('contact_institution')->nullable();
            $table->string('contact_country')->nullable();
            $table->string('contact_phone')->nullable();
            $table->string('contact_email')->nullable();

            // MODERATOR
            $table->string('moderator_name')->nullable();
            $table->string('moderator_position')->nullable();
            $table->string('moderator_institution')->nullable();
            $table->string('moderator_country')->nullable();

            // SPEAKERS
            $table->json('speakers')->nullable();

            // CONTENT
            $table->text('description')->nullable();
            $table->text('learning_objectives')->nullable();

            // STATUS
            $table->string('status')->default('Submitted');
            
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
        Schema::dropIfExists('panels');
    }
}
