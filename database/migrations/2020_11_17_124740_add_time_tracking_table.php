<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTimeTrackingTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('time_tracking', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->unsignedBigInteger('repair_id')->default(0);
            $table->unsignedBigInteger('employee_id')->default(0);

            $table->date('worked_on')->nullable();
            $table->string('description')->default("");
            
            $table->integer('work_time_from_h')->default(0);
            $table->integer('work_time_from_m')->default(0);
            $table->integer('work_time_till_h')->default(0);
            $table->integer('work_time_till_m')->default(0);
            
            $table->integer('drive_to_h')->default(0);
            $table->integer('drive_to_m')->default(0);
            $table->decimal('drive_to_km', 8, 2)->nullable();

            $table->integer('drive_from_h')->default(0);
            $table->integer('drive_from_m')->default(0);
            $table->decimal('drive_from_km', 8, 2)->nullable();
            
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
        Schema::dropIfExists('time_tracking');
    }
}
