<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

use App\ExtraService;
use \Illuminate\Support\Facades\DB;
class ChangeUnitColumnOnExtraServicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('extra_services', function (Blueprint $table) {
            $table->integer('unit_id')->nullable()->after("unit");
        });

//        ExtraService::where('unit', 'kg')->update(['unit_id' => 1]);
//        ExtraService::where('unit', 'Stk')->update(['unit_id' => 2]);
//        ExtraService::where('unit', 'ml')->update(['unit_id' => 3]);
//        ExtraService::where('unit', 'Meter')->update(['unit_id' => 4]);

        Schema::table('extra_services', function (Blueprint $table) {
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
        Schema::table('extra_services', function (Blueprint $table) {
            $table->enum('unit', ['Kg', 'Stk', 'Meter', 'ml'])->default('Stk');
        });

//        ExtraService::where('unit_id', 1)->update(['unit' => "Kg"]);
//        ExtraService::where('unit_id', 2)->update(['unit' => "Stk"]);
//        ExtraService::where('unit_id', 3)->update(['unit' => "ml"]);
//        ExtraService::where('unit_id', 4)->update(['unit' => "Meter"]);

        Schema::table('extra_services', function (Blueprint $table) {
            $table->dropColumn('unit_id');
        });
    }
}
