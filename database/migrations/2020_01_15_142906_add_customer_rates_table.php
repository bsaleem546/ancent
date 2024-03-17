<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCustomerRatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('customer_rates', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->unsignedBigInteger('customer_id');

            $table->string('name')->default("");
            $table->boolean('checked')->default(0);
            $table->string('valid_from')->default("");
            $table->string('valid_to')->default("");
            $table->decimal('travel_costs', 8, 2)->default(0.00);
            $table->decimal('work_costs', 8, 2)->default(0.00);

            $table->integer('due_days')->default(0);
            $table->integer('discount_days')->default(0);
            $table->decimal('discount_amount', 15, 2)->default(0.00);

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
        Schema::dropIfExists('customer_rates');
    }
}
