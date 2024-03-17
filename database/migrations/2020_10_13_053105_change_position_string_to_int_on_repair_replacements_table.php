<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangePositionStringToIntOnRepairReplacementsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Does not work due to the unit enum in the table
        // Schema::table('repair_replacements', function (Blueprint $table) {
        //     $table->integer('position')->change();
        // });
        DB::statement('ALTER TABLE repair_replacements MODIFY position INT;');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Does not work due to the unit enum in the table
        // Schema::table('repair_replacements', function (Blueprint $table) {
        //     $table->string('position')->change();
        // });
        DB::statement('ALTER TABLE repair_replacements MODIFY position VARCHAR(255);');
    }
}
