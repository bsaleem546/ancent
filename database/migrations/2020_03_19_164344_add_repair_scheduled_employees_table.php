<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddRepairScheduledEmployeesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('repair_scheduled_employees', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->unsignedBigInteger('repair_id')->default(0);
            $table->unsignedBigInteger('employee_id')->default(0);

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
        Schema::dropIfExists('repair_scheduled_employees');
    }
}
