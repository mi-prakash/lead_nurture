<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAppointmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('appointments', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('appointment_id')->unique();
            $table->integer('lead_id');
            $table->string('date');
            $table->string('date_time');
            $table->string('time');
            $table->string('end_time');
            $table->string('date_created');
            $table->string('date_time_created');
            $table->float('price', 8, 2);
            $table->float('price_sold', 8, 2);
            $table->string('paid');
            $table->float('amount_paid', 8, 2);
            $table->string('type');
            $table->bigInteger('appointment_type_id');
            $table->bigInteger('calendar_id');
            $table->string('timezone');
            $table->string('calendar_timezone');
            $table->tinyInteger('canceled')->nullable();
            $table->tinyInteger('can_client_cancel');
            $table->tinyInteger('can_client_reschedule');
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
        Schema::dropIfExists('appointments');
    }
}
