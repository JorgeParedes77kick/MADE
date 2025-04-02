<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RolesMenusSeeder extends Seeder {
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run() {

        // DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // DB::table('roles_menus')->truncate(); // Reinicia los IDs
        DB::table('roles_menus')->insertOrIgnore([
            ['id' => 1, 'rol_id' => 1, 'menu_id' => 2],
            ['id' => 2, 'rol_id' => 1, 'menu_id' => 3],
            ['id' => 3, 'rol_id' => 1, 'menu_id' => 4],
            ['id' => 4, 'rol_id' => 1, 'menu_id' => 5],
            ['id' => 5, 'rol_id' => 1, 'menu_id' => 6],
            ['id' => 6, 'rol_id' => 1, 'menu_id' => 7],
            ['id' => 7, 'rol_id' => 1, 'menu_id' => 8],
            ['id' => 8, 'rol_id' => 1, 'menu_id' => 9],
            ['id' => 9, 'rol_id' => 1, 'menu_id' => 10],
            ['id' => 10, 'rol_id' => 1, 'menu_id' => 11],
            ['id' => 11, 'rol_id' => 1, 'menu_id' => 12],
            ['id' => 12, 'rol_id' => 1, 'menu_id' => 13],
            ['id' => 13, 'rol_id' => 1, 'menu_id' => 14],
            ['id' => 14, 'rol_id' => 1, 'menu_id' => 15],
            ['id' => 15, 'rol_id' => 1, 'menu_id' => 16],
            ['id' => 16, 'rol_id' => 1, 'menu_id' => 17],
            ['id' => 17, 'rol_id' => 1, 'menu_id' => 18],
            ['id' => 18, 'rol_id' => 1, 'menu_id' => 19],
            ['id' => 19, 'rol_id' => 1, 'menu_id' => 20],
            ['id' => 20, 'rol_id' => 1, 'menu_id' => 21],
            ['id' => 21, 'rol_id' => 1, 'menu_id' => 22],
            ['id' => 22, 'rol_id' => 5, 'menu_id' => 23],
            ['id' => 23, 'rol_id' => 4, 'menu_id' => 24],
            ['id' => 24, 'rol_id' => 4, 'menu_id' => 21],
            ['id' => 25, 'rol_id' => 3, 'menu_id' => 21],
            ['id' => 26, 'rol_id' => 2, 'menu_id' => 21],
            ['id' => 27, 'rol_id' => 2, 'menu_id' => 9],
            ['id' => 28, 'rol_id' => 2, 'menu_id' => 11],
            ['id' => 29, 'rol_id' => 2, 'menu_id' => 1],
            ['id' => 30, 'rol_id' => 3, 'menu_id' => 1],
            ['id' => 31, 'rol_id' => 4, 'menu_id' => 1],
            ['id' => 32, 'rol_id' => 5, 'menu_id' => 1],
        ]);

        // DB::statement('SET FOREIGN_KEY_CHECKS=1;');

    }
}
