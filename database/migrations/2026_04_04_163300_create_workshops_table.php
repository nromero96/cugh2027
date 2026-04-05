<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWorkshopsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('workshops', function (Blueprint $table) {
            $table->id();

            // Lead Contact Person
            $table->string('lead_name');
            $table->string('lead_institution');
            $table->string('lead_title');
            $table->string('lead_email');
            $table->string('lead_phone');
            $table->string('lead_cell');

            // Workshop Program
            $table->string('workshop_title');
            $table->text('workshop_desc');
            $table->text('workshop_objectives');
            $table->text('workshop_speakers')->nullable();

            // Room Options
            $table->string('time_slot');
            $table->string('day_length');
            $table->string('room_setup');
            $table->integer('attendees');
            $table->text('notes')->nullable();

            // Payment Lead
            $table->boolean('payment_lead_same')->default(true);
            $table->string('payment_name')->nullable();
            $table->string('payment_institution')->nullable();
            $table->string('payment_title')->nullable();
            $table->string('payment_email')->nullable();
            $table->string('payment_phone')->nullable();
            $table->string('payment_cell')->nullable();

            // Terms and Signature
            $table->string('signature_path'); // ruta de la imagen de firma
            $table->string('place_date');

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
        Schema::dropIfExists('workshops');
    }
}
