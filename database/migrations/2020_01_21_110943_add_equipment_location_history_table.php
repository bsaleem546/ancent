<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddEquipmentLocationHistoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('equipment_location_history', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->unsignedBigInteger('equipment_id');
            $table->unsignedBigInteger('location_id');
            $table->unsignedBigInteger('customer_id');
            $table->unsignedBigInteger('operator_id');

            $table->date('from')->nullable();
            $table->date('to')->nullable();

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
        Schema::dropIfExists('equipment_location_history');
    }
}
