<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFlightsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('flights', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('event_id');
            $table->unsignedInteger('pilot_id')->nullable();
            $table->string('callsign', 10);
            $table->unsignedInteger('origin_airport_id');
            $table->unsignedInteger('destination_airport_id');
            $table->time('departure_time');
            $table->time('arrival_time');
            $table->text('route')->nullable();
            $table->string('aircraft_type_icao')->nullable();
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
        Schema::dropIfExists('flights');
    }
}
