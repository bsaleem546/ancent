<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeCountToDecimalOnExtraServicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Does not work due to the unit enum in the table
        // Schema::table('extra_services', function (Blueprint $table) {
        //     $table->decimal('count', 15, 2)->change();
        // });
        DB::statement('ALTER TABLE extra_services MODIFY `count` DECIMAL(15,2);');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Does not work due to the unit enum in the table
        // Schema::table('extra_services', function (Blueprint $table) {
        //     $table->integer('count')->change();
        // });
        DB::statement('ALTER TABLE extra_services MODIFY `count` INT;');
    }
}
