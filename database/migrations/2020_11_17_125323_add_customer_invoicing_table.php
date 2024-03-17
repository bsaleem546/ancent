<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCustomerInvoicingTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('customer_invoicing', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->unsignedBigInteger('repair_id')->default(0);
            $table->unsignedBigInteger('employee_id')->default(0);

            $table->date('worked_on')->nullable();
            $table->string('description')->default("");

            $table->integer('work_h')->default(0);
            $table->integer('work_min')->default(0);

            $table->boolean('internal')->nullable();
            $table->boolean('driving_time')->nullable();
            
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
        Schema::dropIfExists('customer_invoicing');
    }
}
