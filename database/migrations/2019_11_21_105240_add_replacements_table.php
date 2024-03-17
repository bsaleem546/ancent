<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddReplacementsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('replacements', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->unsignedBigInteger('group_id')->default(0);

            $table->string('number_id')->default("");
            $table->string('description')->default("");
            $table->decimal('price', 15, 2)->default(0.00);
            $table->decimal('discount', 5, 2)->default(0.00);
            $table->enum('unit', ['Stk.', 'Kg'])->default('Stk.');

            $table->timestamps();
        });

        Schema::create('repair_replacements', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->unsignedBigInteger('repair_id')->default(0);

            $table->string('position')->nullable();
            $table->integer('count')->nullable();
            $table->string('number_id')->default("");
            $table->string('description')->default("");
            $table->decimal('price', 15, 2)->nullable();
            $table->decimal('discount', 5, 2)->nullable();
            $table->enum('unit', ['Stk.', 'Kg'])->default('Stk.');

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
        Schema::dropIfExists('replacements');
        Schema::dropIfExists('repair_replacements');
    }
}
