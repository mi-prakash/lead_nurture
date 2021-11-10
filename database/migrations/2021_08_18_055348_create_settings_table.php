<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id')->unique();
            $table->string('click_funnel_email')->nullable();
            $table->string('click_funnel_api_key')->nullable();
            $table->string('click_funnel_webhook_url')->nullable();
            $table->string('click_funnel_name')->nullable();
            $table->string('click_funnel_id')->nullable();
            $table->string('acuity_user_id')->nullable();
            $table->string('acuity_api_key')->nullable();
            $table->string('acuity_webhook_url')->nullable();
            $table->string('acuity_calendar_name')->nullable();
            $table->string('acuity_calendar_id')->nullable();
            $table->string('twilio_account_sid')->nullable();
            $table->string('twilio_auth_token')->nullable();
            $table->string('twilio_number')->nullable();
            $table->string('twilio_webhook_url')->nullable();
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
        Schema::dropIfExists('settings');
    }
}
