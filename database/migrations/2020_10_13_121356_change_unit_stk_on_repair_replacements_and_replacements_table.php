<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use \Illuminate\Support\Facades\DB;

use App\Replacement;
use App\RepairReplacement;

class ChangeUnitStkOnRepairReplacementsAndReplacementsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // DB::statement("ALTER TABLE repair_replacements MODIFY unit ENUM('Stk', 'Kg') DEFAULT 'Stk';");
        // DB::statement("ALTER TABLE replacements MODIFY unit ENUM('Stk', 'Kg') DEFAULT 'Stk';");

        // Include old and new enum values
        DB::statement("ALTER TABLE replacements MODIFY COLUMN unit ENUM('Stk.', 'Kg', 'Stk', 'Meter', 'ml')");
        // Replace Stk. with Stk
//        Replacement::where('unit', 'Stk.')->update(['unit' => 'Stk']);
        // Delete old values
        DB::statement("ALTER TABLE replacements MODIFY COLUMN unit ENUM('Kg', 'Stk', 'Meter', 'ml') DEFAULT 'Stk'");

        // Include old and new enum values
        DB::statement("ALTER TABLE repair_replacements MODIFY COLUMN unit ENUM('Stk.', 'Kg', 'Stk', 'Meter', 'ml')");
        // Replace Stk. with Stk
//        RepairReplacement::where('unit', 'Stk.')->update(['unit' => 'Stk']);
        // Delete old values
        DB::statement("ALTER TABLE repair_replacements MODIFY COLUMN unit ENUM('Kg', 'Stk', 'Meter', 'ml') DEFAULT 'Stk'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // DB::statement("ALTER TABLE repair_replacements MODIFY unit ENUM('Stk.', 'Kg') DEFAULT 'Stk.';");
        // DB::statement("ALTER TABLE replacements MODIFY unit ENUM('Stk.', 'Kg') DEFAULT 'Stk.';");

        // Include old and new enum values
        DB::statement("ALTER TABLE replacements MODIFY COLUMN unit ENUM('Kg', 'Stk', 'Meter', 'ml', 'Stk.')");
        // Replace Stk. with Stk
        Replacement::where('unit', 'Stk')->update(['unit' => 'Stk.']);
        // Delete old values
        DB::statement("ALTER TABLE replacements MODIFY COLUMN unit ENUM('Kg', 'Stk.') DEFAULT 'Stk.'");

        // Include old and new enum values
        DB::statement("ALTER TABLE repair_replacements MODIFY COLUMN unit ENUM('kg', 'Stk', 'Meter', 'ml', 'Stk.')");
        // Replace Stk. with Stk
        RepairReplacement::where('unit', 'Stk')->update(['unit' => 'Stk.']);
        // Delete old values
        DB::statement("ALTER TABLE repair_replacements MODIFY COLUMN unit ENUM('Stk.', 'Kg') DEFAULT 'Stk.'");
    }
}
