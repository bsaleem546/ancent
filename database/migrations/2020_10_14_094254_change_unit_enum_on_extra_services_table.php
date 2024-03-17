<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use \Illuminate\Support\Facades\DB;

use App\ExtraService;

class ChangeUnitEnumOnExtraServicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Include old and new enum values
        DB::statement("ALTER TABLE extra_services MODIFY COLUMN unit ENUM('Stk.', 'Kg', 'Stk', 'Meter', 'ml')");
        // Replace Stk. with Stk
//        ExtraService::where('unit', 'Stk.')->update(['unit' => 'Stk']);
        // Delete old values
        DB::statement("ALTER TABLE extra_services MODIFY COLUMN unit ENUM('Kg', 'Stk', 'Meter', 'ml') DEFAULT 'Stk'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Include old and new enum values
        DB::statement("ALTER TABLE extra_services MODIFY COLUMN unit ENUM('Kg', 'Stk', 'Meter', 'ml', 'Stk.')");
        // Replace Stk. with Stk
//        ExtraService::where('unit', 'Stk')->update(['unit' => 'Stk.']);
        // Delete old values
        DB::statement("ALTER TABLE extra_services MODIFY COLUMN unit ENUM('Kg', 'Stk.') DEFAULT 'Stk.'");
    }
}
