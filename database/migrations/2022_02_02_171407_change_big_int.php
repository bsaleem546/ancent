<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeBigInt extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('units', function (Blueprint $table)
        {
            $table->increments('id')->change();
        });

        Schema::table('time_tracking', function (Blueprint $table)
        {
            $table->increments('id')->change();
            $table->integer('repair_id')->change();
            $table->integer('employee_id')->change();
        });

        // number too big, cannot be converted to integer
        // Schema::table('repairs', function (Blueprint $table)
        // {
        //     $table->integer('rate_id')->change();
        // });

        Schema::table('invoices', function (Blueprint $table)
        {
            $table->integer('company_id')->change();
        });

        // number too big, cannot be converted to integer
        // Schema::table('customer_rates', function (Blueprint $table)
        // {
        //     $table->increments('id')->change();
        // });

        Schema::table('customer_invoicing', function (Blueprint $table)
        {
            $table->increments('id')->change();
            $table->integer('repair_id')->change();
            $table->integer('employee_id')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
