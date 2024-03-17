<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddReplacementDiscountToRepairReplacementsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('repair_replacements', function (Blueprint $table) {
            $table->decimal('replacement_discount', 5, 2)->nullable()->after("discount");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('repair_replacements', function (Blueprint $table) {
            $table->dropColumn('replacement_discount');
        });
    }
}
