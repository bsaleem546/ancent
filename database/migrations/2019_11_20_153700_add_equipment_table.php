<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddEquipmentTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('equipment', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->string('number')->default("");
            $table->string('manufacture_year')->default("");
            $table->string('manufacture_no')->default("");
            $table->date('next_uvv')->nullable();
            $table->date('next_checkup')->nullable();

            $table->unsignedBigInteger('model_id')->default(0)->comment('Anlagenart');
            $table->unsignedBigInteger('type_id')->default(0)->comment('Anlagentyp');
            $table->unsignedBigInteger('manufacturer_id')->default(0);
            $table->unsignedBigInteger('location_id')->default(0);
            $table->unsignedBigInteger('customer_id')->default(0);
            $table->unsignedBigInteger('operator_id')->default(0);

            $table->date('equipment_location_from')->nullable();

            $table->text('notes')->nullable();
            $table->text('internal_notes')->nullable();

            $table->boolean('active')->default(1);

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
        Schema::dropIfExists('equipment');
    }
}
