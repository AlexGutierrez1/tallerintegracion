<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRelationshipFieldsToAppointmentActivityTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('appointment_activity', function (Blueprint $table) {
            $table->foreign('appointment_id', 'fk_appointment_history_appointment_id')->references('id')->on('appointment');
            $table->foreign('user_id', 'fk_appointment_history_user_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('appointment_activity_', function (Blueprint $table) {
            //
        });
    }
}
