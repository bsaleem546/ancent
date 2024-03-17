<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class AddPermissions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Create the super user
        $superUserRole = Role::create(['name' => 'super_user']);

        $rights = ['read', 'write', 'create', 'delete'];
        $objects = ['customers', 'operators', 'equipment', 'locations', 'repairs', 'repair_details', 'uvv'];

        foreach($rights as $right)
        {
            foreach($objects as $object)
            {
                Permission::create(['name' => $right.' '.$object])->assignRole($superUserRole);
            }
        }

        Permission::create(['name' => 'access_um'])->assignRole($superUserRole);
        Permission::create(['name' => 'access_internal_notes'])->assignRole($superUserRole);
        Permission::create(['name' => 'access_prices_offer'])->assignRole($superUserRole);
        Permission::create(['name' => 'access_prices_invoice'])->assignRole($superUserRole);

        Schema::table('users', function (Blueprint $table) {
            $table->boolean('is_super_admin');
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
