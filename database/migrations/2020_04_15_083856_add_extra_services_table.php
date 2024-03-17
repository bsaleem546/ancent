<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddExtraServicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('extra_services', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->unsignedBigInteger('invoice_id')->default(0);

            $table->string('position')->nullable();
            $table->string('name')->nullable();
            $table->integer('count')->nullable();
            $table->enum('unit', ['Stk.', 'Kg'])->default('Stk.');
            $table->decimal('discount', 5, 2)->nullable();
            $table->decimal('price', 15, 2)->nullable();
            
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
        Schema::dropIfExists('extra_services');
    }
}
