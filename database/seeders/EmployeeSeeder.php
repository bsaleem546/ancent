<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EmployeeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('employees')->insert([
            [
                'first_name' => 'John',
                'last_name' => 'Doe',
                'email' => 'jd@a.de',
                'phone' => '',
                'fax' => '',
                'sms' => '',
                'notes' => ''
            ],
            [
                'first_name' => 'Jane',
                'last_name' => 'Doe',
                'email' => 'janed@a.de',
                'phone' => '',
                'fax' => '',
                'sms' => '',
                'notes' => ''
            ],
            [
                'first_name' => 'Steve',
                'last_name' => 'Jobs',
                'email' => 'sj@a.de',
                'phone' => '',
                'fax' => '',
                'sms' => '',
                'notes' => ''
            ],
            [
                'first_name' => 'Bill',
                'last_name' => 'Gates',
                'email' => 'bg@a.de',
                'phone' => '',
                'fax' => '',
                'sms' => '',
                'notes' => ''
            ],
            [
                'first_name' => 'Elon',
                'last_name' => 'Musk',
                'email' => 'em@a.de',
                'phone' => '',
                'fax' => '',
                'sms' => '',
                'notes' => ''
            ]
        ]);
    }
}
