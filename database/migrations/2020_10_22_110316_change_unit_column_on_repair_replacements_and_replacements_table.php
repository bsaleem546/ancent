<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

use App\Replacement;
use App\RepairReplacement;

class ChangeUnitColumnOnRepairReplacementsAndReplacementsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('repair_replacements', function (Blueprint $table) {
            $table->integer('unit_id')->nullable()->after("unit");
        });
        Schema::table('replacements', function (Blueprint $table) {
            $table->integer('unit_id')->nullable()->after("unit");
        });

//        Replacement::where('unit', 'kg')->update(['unit_id' => 1]);
//        Replacement::where('unit', 'Stk')->update(['unit_id' => 2]);
//        Replacement::where('unit', 'ml')->update(['unit_id' => 3]);
//        Replacement::where('unit', 'Meter')->update(['unit_id' => 4]);
//        RepairReplacement::where('unit', 'kg')->update(['unit_id' => 1]);
//        RepairReplacement::where('unit', 'Stk')->update(['unit_id' => 2]);
//        RepairReplacement::where('unit', 'ml')->update(['unit_id' => 3]);
//        RepairReplacement::where('unit', 'Meter')->update(['unit_id' => 4]);

        Schema::table('repair_replacements', function (Blueprint $table) {
            $table->dropColumn('unit');
        });
        Schema::table('replacements', function (Blueprint $table) {
            $table->dropColumn('unit');
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
            $table->enum('unit', ['Kg', 'Stk', 'Meter', 'ml'])->default('Stk');
        });
        Schema::table('replacements', function (Blueprint $table) {
            $table->enum('unit', ['Kg', 'Stk', 'Meter', 'ml'])->default('Stk');
        });

//        Replacement::where('unit_id', 1)->update(['unit' => "Kg"]);
//        Replacement::where('unit_id', 2)->update(['unit' => "Stk"]);
//        Replacement::where('unit_id', 3)->update(['unit' => "ml"]);
//        Replacement::where('unit_id', 4)->update(['unit' => "Meter"]);
//        RepairReplacement::where('unit_id', 1)->update(['unit' => "Kg"]);
//        RepairReplacement::where('unit_id', 2)->update(['unit' => "Stk"]);
//        RepairReplacement::where('unit_id', 3)->update(['unit' => "ml"]);
//        RepairReplacement::where('unit_id', 4)->update(['unit' => "Meter"]);

        Schema::table('repair_replacements', function (Blueprint $table) {
            $table->dropColumn('unit_id');
        });
        Schema::table('replacements', function (Blueprint $table) {
            $table->dropColumn('unit_id');
        });
    }
}
