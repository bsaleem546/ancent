<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EquipmentRelatedSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('equipment_manufacturer')->insert([
            ['description' => 'Ford'],
            ['description' => 'Skoda'],
            ['description' => 'WV'],
            ['description' => 'Audi'],
            ['description' => 'Tesla'],
        ]);

        DB::table('equipment_type')->insert([
            ['description' => 'Sedan'],
            ['description' => 'Medium'],
            ['description' => 'Small'],
            ['description' => 'SUV'],
            ['description' => 'Electric'],
        ]);

        DB::table('replacement_groups')->insert([
            ['name' => 'Gruppe 1'],
            ['name' => 'Gruppe 2'],
            ['name' => 'Gruppe 3'],
            ['name' => 'Gruppe 4']
        ]);

        DB::table('replacements')->insert([
            [
                'group_id' => 1,
                'number_id' => 'EQ11111',
                'description' => 'Description 1 for group 1',
                'description2' => 'Description 2 for group 1',
                'price' => 11.50,
                'discount' => 5,
                'unit_id' => 1
            ],
            [
                'group_id' => 2,
                'number_id' => 'EQ12222',
                'description' => 'Description 1 for group 2',
                'description2' => 'Description 2 for group 2',
                'price' => 22.50,
                'discount' => 10,
                'unit_id' => 2
            ],[
                'group_id' => 3,
                'number_id' => 'EQ3333',
                'description' => 'Description 1 for group 3',
                'description2' => 'Description 2 for group 3',
                'price' => 33.50,
                'discount' => 20,
                'unit_id' => 1
            ],[
                'group_id' => 2,
                'number_id' => 'EQ44444',
                'description' => 'Description 1 for group 2',
                'description2' => 'Description 2 for group 2',
                'price' => 27.50,
                'discount' => 50,
                'unit_id' => 2
            ],[
                'group_id' => 1,
                'number_id' => 'EQ5555',
                'description' => 'Description 1 EQ5555 for group 1',
                'description2' => 'Description 2 EQ5555 for group 1',
                'price' => 17.50,
                'discount' => 12,
                'unit_id' => 3
            ],[
                'group_id' => 4,
                'number_id' => 'EQ66666',
                'description' => 'Description 1 for group 4',
                'description2' => 'Description 2 for group 4',
                'price' => 16.50,
                'discount' => 5,
                'unit_id' => 2
            ],[
                'group_id' => 0,
                'number_id' => 'EQ11111',
                'description' => 'Description 1 for group 0',
                'description2' => 'Description 2 for group 0',
                'price' => 11.50,
                'discount' => 5,
                'unit_id' => 1
            ],
        ]);
    }
}
