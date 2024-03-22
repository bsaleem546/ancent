<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
//        DB::table('roles')->truncate();
//        DB::table('role_has_permissions')->truncate();

        DB::query('TRUNCATE TABLE model_has_roles');

        DB::query("INSERT INTO `roles` (`id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES
            (1, 'super_user', 'web', '2024-02-09 02:15:49', '2024-02-09 02:15:49')");

        DB::query("INSERT INTO `role_has_permissions` (`permission_id`, `role_id`) VALUES
                (1, 1),
                (2, 1),
                (3, 1),
                (4, 1),
                (5, 1),
                (6, 1),
                (7, 1),
                (8, 1),
                (9, 1),
                (10, 1),
                (11, 1),
                (12, 1),
                (13, 1),
                (14, 1),
                (15, 1),
                (16, 1),
                (17, 1),
                (18, 1),
                (19, 1),
                (20, 1),
                (21, 1),
                (22, 1),
                (23, 1),
                (24, 1),
                (25, 1),
                (26, 1),
                (27, 1),
                (28, 1),
                (29, 1),
                (30, 1),
                (31, 1),
                (32, 1),
                (33, 1),
                (34, 1),
                (35, 1),
                (36, 1),
                (37, 1),
                (38, 1),
                (39, 1),
                (40, 1),
                (41, 1),
                (42, 1),
                (43, 1),
                (44, 1),
                (45, 1),
                (46, 1),
                (47, 1),
                (48, 1),
                (49, 1),
                (50, 1),
                (51, 1),
                (52, 1),
                (53, 1),
                (54, 1),
                (55, 1)");
    }
}
