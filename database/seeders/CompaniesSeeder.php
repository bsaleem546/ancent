<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CompaniesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('companies')->insert([
            [
                'name' => 'Ansent GmbH & Co.KG',
                'street' => 'Sachsenspiegelstraße 26',
                'pc_town' => 'D-80995 München',

                'phone' => '+49 (0) 89 / 15 89 16 70',
                'fax' => '+49 (0) 89 / 15 89 16 70',
                'email' => 'info@ansent.de',

                'turnover_tax_id' => 'USt-IdNr. DE 219227671',
                'company_reg_nr' => 'HRA 78636, Amtsgericht München',
                'ceo' => 'M-A. Reichel',

                'personally_liable_partner_name' => 'ANSENT Vermietungs-GmbH',
                'personally_liable_partner_reg_nr' => 'HRB 182974, Amtsgericht München',

                'bank' => 'Raiffeisenbank München Nord',
                'bank_id' => '701 694 65 KNR: 43 91 85',
                'iban' => 'DE 12 7016 9465 0000 4391 85',
                'bic' => 'GENODEF1M08',

                'logo' => 'ansent'
            ],
            [
                'name' => 'Ansent GmbH & Co.KG 1',
                'street' => 'Sachsenspiegelstraße 26',
                'pc_town' => 'D-80995 München',

                'phone' => '+49 (0) 89 / 15 89 16 70',
                'fax' => '+49 (0) 89 / 15 89 16 70',
                'email' => 'info@ansent.de',

                'turnover_tax_id' => 'USt-IdNr. DE 219227671',
                'company_reg_nr' => 'HRA 78636, Amtsgericht München',
                'ceo' => 'M-A. Reichel',

                'personally_liable_partner_name' => 'ANSENT Vermietungs-GmbH',
                'personally_liable_partner_reg_nr' => 'HRB 182974, Amtsgericht München',

                'bank' => 'Raiffeisenbank München Nord',
                'bank_id' => '701 694 65 KNR: 43 91 85',
                'iban' => 'DE 12 7016 9465 0000 4391 85',
                'bic' => 'GENODEF1M08',

                'logo' => 'ansent1'
            ]
        ]);

        DB::table('customers')->insert([
            [
                'name' => 'Spar',
                'street' => 'Berlin1',
                'postal_code' => '111111',
                'place' => 'Berlin',
                'country' => 'DE',
                'notes' => 'notes',
                'internal_notes' => 'internal notes',
                'id_rw' => '01',
                'supplier_number' => 'SN0001',
                'discount' => 10,
                'contact_person' => 'Michelangelo Spar',
                'active' => 1
            ],
            [
                'name' => 'Profi',
                'street' => 'Berlin2',
                'postal_code' => '222222',
                'place' => 'Berlin',
                'country' => 'DE',
                'notes' => 'notes',
                'internal_notes' => 'internal notes',
                'id_rw' => '02',
                'supplier_number' => 'SN0002',
                'supplier_number' => 'SN0002',
                'discount' => 20,
                'contact_person' => 'Leonardo Profi',
                'active' => 1
            ],
            [
                'name' => 'Prospero',
                'street' => 'Berlin3',
                'postal_code' => '333333',
                'place' => 'Berlin',
                'country' => 'DE',
                'notes' => 'notes',
                'internal_notes' => 'internal notes',
                'id_rw' => '03',
                'supplier_number' => 'SN0003',
                'discount' => 30,
                'contact_person' => 'Rafaello Prospero',
                'active' => 1
            ],
            [
                'name' => 'Kaufland',
                'street' => 'Munich1',
                'postal_code' => '444444',
                'place' => 'Munich',
                'country' => 'DE',
                'notes' => 'notes',
                'internal_notes' => 'internal notes',
                'id_rw' => '04',
                'supplier_number' => 'SN0004',
                'discount' => 40,
                'contact_person' => 'Donatello Kaufland',
                'active' => 1
            ],
            [
                'name' => 'Auchan',
                'street' => 'Munich2',
                'postal_code' => '555555',
                'place' => 'Munich',
                'country' => 'DE',
                'notes' => 'notes',
                'internal_notes' => 'internal notes',
                'id_rw' => '05',
                'supplier_number' => 'SN0005',
                'discount' => 50,
                'contact_person' => 'Schredder Auchan',
                'active' => 1
            ]
        ]);

        DB::table('operators')->insert([
            [
                'name' => 'Main',
                'street' => 'Berlin1',
                'postal_code' => '111111',
                'place' => 'Berlin',
                'country' => 'DE',
                'notes' => 'notes',
                'internal_notes' => 'internal notes',
                'active' => 1
            ],
            [
                'name' => 'Mid',
                'street' => 'Berlin2',
                'postal_code' => '222222',
                'place' => 'Berlin',
                'country' => 'DE',
                'notes' => 'notes',
                'internal_notes' => 'internal notes',
                'active' => 1
            ],
            [
                'name' => 'Maxi',
                'street' => 'Berlin3',
                'postal_code' => '333333',
                'place' => 'Berlin',
                'country' => 'DE',
                'notes' => 'notes',
                'internal_notes' => 'internal notes',
                'active' => 1
            ],
            [
                'name' => 'Min',
                'street' => 'Munich1',
                'postal_code' => '444444',
                'place' => 'Munich',
                'country' => 'DE',
                'notes' => 'notes',
                'internal_notes' => 'internal notes',
                'active' => 1
            ],
            [
                'name' => 'Hiper',
                'street' => 'Munich2',
                'postal_code' => '555555',
                'place' => 'Munich',
                'country' => 'DE',
                'notes' => 'notes',
                'internal_notes' => 'internal notes',
                'active' => 1
            ]
        ]);

        DB::table('locations')->insert([
            [
                'name' => 'Berlin Krausenstraße',
                'street' => 'Krausenstraße',
                'postal_code' => '10117',
                'place' => 'Berlin',
                'country' => 'DE',
                'notes' => 'notes',
                'internal_notes' => 'internal notes',
                'active_travel_cost' => 1,
                'travel_costs' => 135.50,
                'active_km' => 0,
                'km_costs' => 1.98,
                'is_gsm' => 1,
                'active' => 1
            ],
            [
                'name' => 'Berlin Schöneberger',
                'street' => 'Schöneberger',
                'postal_code' => '10963',
                'place' => 'Berlin',
                'country' => 'DE',
                'notes' => 'notes',
                'internal_notes' => 'internal notes',
                'active_travel_cost' => 0,
                'travel_costs' => 135.50,
                'active_km' => 1,
                'km_costs' => 1.98,
                'is_gsm' => 1,
                'active' => 1
            ],
            [
                'name' => 'Berlin Leipziger',
                'street' => 'Leipziger',
                'postal_code' => '10117',
                'place' => 'Berlin',
                'country' => 'DE',
                'notes' => 'notes',
                'internal_notes' => 'internal notes',
                'active_travel_cost' => 1,
                'travel_costs' => 155.75,
                'active_km' => 0,
                'km_costs' => 1.75,
                'is_gsm' => 1,
                'active' => 1
            ],
            [
                'name' => 'Munich1',
                'street' => 'Munich1',
                'postal_code' => '444444',
                'place' => 'Munich',
                'country' => 'DE',
                'notes' => 'notes',
                'internal_notes' => 'internal notes',
                'active_travel_cost' => 0,
                'travel_costs' => 115.45,
                'active_km' => 1,
                'km_costs' => 1.77,
                'is_gsm' => 1,
                'active' => 1
            ],
            [
                'name' => 'Munich2',
                'street' => 'Munich2',
                'postal_code' => '555555',
                'place' => 'Munich',
                'country' => 'DE',
                'notes' => 'notes',
                'internal_notes' => 'internal notes',
                'active_travel_cost' => 0,
                'travel_costs' => 95.50,
                'active_km' => 1,
                'km_costs' => 2,
                'is_gsm' => 1,
                'active' => 1
            ],
            [
                'name' => 'Berlin Franklinstraße',
                'street' => 'Franklinstraße',
                'postal_code' => '10587',
                'place' => 'Berlin',
                'country' => 'DE',
                'notes' => 'notes',
                'internal_notes' => 'internal notes',
                'active_travel_cost' => 1,
                'travel_costs' => 59.99,
                'active_km' => 1,
                'km_costs' => 49.99,
                'is_gsm' => 1,
                'active' => 1
            ]
        ]);

        DB::table('replacements')->insert([
            [
                'group_id' => 1,
                'number_id' => 'EQ11111',
                'description' => 'description',
                'description2' => 'description2',
                'price' => 10,
                'discount' => 1,
                'unit_id' => 1
            ],
            [
                'group_id' => 1,
                'number_id' => 'EQ2222',
                'description' => 'description',
                'description2' => 'description2',
                'price' => 20,
                'discount' => 2,
                'unit_id' => 1
            ],
            [
                'group_id' => 2,
                'number_id' => 'EQ3333',
                'description' => 'description',
                'description2' => 'description2',
                'price' => 30,
                'discount' => 3,
                'unit_id' => 1
            ],
            [
                'group_id' => 3,
                'number_id' => 'EQ14444',
                'description' => 'description',
                'description2' => 'description2',
                'price' => 40,
                'discount' => 4,
                'unit_id' => 0
            ],
            [
                'group_id' => 4,
                'number_id' => 'EQ15555',
                'description' => 'description',
                'description2' => 'description2',
                'price' => 50,
                'discount' => 5,
                'unit_id' => 0
            ]
        ]);
    }
}
