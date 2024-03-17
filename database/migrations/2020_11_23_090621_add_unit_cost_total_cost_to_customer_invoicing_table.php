<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddUnitCostTotalCostToCustomerInvoicingTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('customer_invoicing', function (Blueprint $table) {
            $table->decimal('unit_cost', 8, 2)->nullable()->after("work_min");
            $table->decimal('total_cost', 8, 2)->nullable()->after("unit_cost");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('customer_invoicing', function (Blueprint $table) {
            $table->dropColumn('unit_cost');
            $table->dropColumn('total_cost');
        });
    }
}
