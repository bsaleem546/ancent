<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class AddNewPermissions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Get the super user
        $superUserRole = Role::findByName('super_user');

        $rights = ['read', 'write', 'create', 'delete'];
        $objects = ['repair_list', 'invoices', 'rentals', 'rental_invoices', 'offers'];

        foreach($rights as $right)
        {
            foreach($objects as $object)
            {
                Permission::create(['name' => $right.' '.$object])->assignRole($superUserRole);
            }
        }

        Permission::create(['name' => 'access_time_tracking'])->assignRole($superUserRole);
        Permission::create(['name' => 'access_manual_invoice'])->assignRole($superUserRole);
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
