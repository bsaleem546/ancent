<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddUserDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_details', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->unsignedBigInteger('user_id');
            
            $table->string('first_name')->default("");
            $table->string('last_name')->default("");
            $table->string('email')->default("");
            $table->string('phone')->default("");
            $table->string('fax')->default("");
            $table->string('sms')->default("");
            $table->text('notes')->nullable();

            $table->unsignedBigInteger('customer_id')->default(0);
            $table->unsignedBigInteger('operator_id')->default(0);
            $table->enum('customer_operator_filter', ['OR', 'AND'])->default('OR')->comment('Used to filter the customer/operator data that the user sees');

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
        Schema::dropIfExists('user_details');
    }
}
