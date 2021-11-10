<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class AlterDateTimeAtAppointmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("ALTER TABLE `appointments` CHANGE `date_time` `date_time` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP, CHANGE `date_time_created` `date_time_created` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP;");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
