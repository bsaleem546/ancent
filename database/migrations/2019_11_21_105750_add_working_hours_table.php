<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddWorkingHoursTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('working_hours', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->unsignedBigInteger('repair_id')->default(0);
            $table->unsignedBigInteger('employee_id')->default(0);

            $table->date('worked_on')->nullable();
            $table->string('description')->default("");
            $table->integer('work_h')->default(0);
            $table->integer('work_min')->default(0);
            $table->integer('travel_h')->default(0);
            $table->integer('travel_min')->default(0);
            
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
        Schema::dropIfExists('working_hours');
    }
}
