<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\UserDetails;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = new User();
        $user->api_token = Str::random(60);
        $user->password = Hash::make("admin_001");
        $user->username       = "admin";
        $user->email          = "dev@netzinkubator.de";
        $user->is_super_admin = true;

        $user->assignRole('super_user');
        $user->save();


        // We also need to create the user details entry for this new user
        $userDetails = new UserDetails();
        $userDetails->first_name = "admin";

        $user->userDetails()->save($userDetails);
    }
}
