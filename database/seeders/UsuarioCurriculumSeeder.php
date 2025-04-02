<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UsuarioCurriculumSeeder extends Seeder {
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run() {
        DB::table('usuario_curriculums')->truncate(); // Reinicia los IDs si es necesario
        DB::table('usuario_curriculums')->insertOrIgnore([
            ['usuario_id' => 3, 'curriculum_id' => 4],
            ['usuario_id' => 7, 'curriculum_id' => 6],
            ['usuario_id' => 8, 'curriculum_id' => 2],
            ['usuario_id' => 9, 'curriculum_id' => 4],
            ['usuario_id' => 10, 'curriculum_id' => 10],
            ['usuario_id' => 11, 'curriculum_id' => 11],
            ['usuario_id' => 12, 'curriculum_id' => 8],
            ['usuario_id' => 13, 'curriculum_id' => 7],
            ['usuario_id' => 14, 'curriculum_id' => 3],
            ['usuario_id' => 15, 'curriculum_id' => 1],
            ['usuario_id' => 16, 'curriculum_id' => 5],
            ['usuario_id' => 17, 'curriculum_id' => 16],
            ['usuario_id' => 18, 'curriculum_id' => 14],
            ['usuario_id' => 19, 'curriculum_id' => 15],
            ['usuario_id' => 20, 'curriculum_id' => 13],
            ['usuario_id' => 22, 'curriculum_id' => 6],
            ['usuario_id' => 23, 'curriculum_id' => 6],
            ['usuario_id' => 24, 'curriculum_id' => 7],
            ['usuario_id' => 25, 'curriculum_id' => 7],
            ['usuario_id' => 26, 'curriculum_id' => 16],
            ['usuario_id' => 27, 'curriculum_id' => 5],
            ['usuario_id' => 28, 'curriculum_id' => 5],
            ['usuario_id' => 29, 'curriculum_id' => 6],
            ['usuario_id' => 30, 'curriculum_id' => 11],
            ['usuario_id' => 31, 'curriculum_id' => 11],
            ['usuario_id' => 32, 'curriculum_id' => 8],
            ['usuario_id' => 33, 'curriculum_id' => 14],
            ['usuario_id' => 34, 'curriculum_id' => 14],
            ['usuario_id' => 35, 'curriculum_id' => 13],
            ['usuario_id' => 36, 'curriculum_id' => 15],
            ['usuario_id' => 37, 'curriculum_id' => 2],
            ['usuario_id' => 38, 'curriculum_id' => 3],
            ['usuario_id' => 39, 'curriculum_id' => 1],
            ['usuario_id' => 40, 'curriculum_id' => 1],
            ['usuario_id' => 41, 'curriculum_id' => 7],
            ['usuario_id' => 42, 'curriculum_id' => 17],
            ['usuario_id' => 43, 'curriculum_id' => 18],
            ['usuario_id' => 44, 'curriculum_id' => 20],
            ['usuario_id' => 45, 'curriculum_id' => 22],
            ['usuario_id' => 46, 'curriculum_id' => 21],
            ['usuario_id' => 48, 'curriculum_id' => 24],
            ['usuario_id' => 49, 'curriculum_id' => 18],
            ['usuario_id' => 50, 'curriculum_id' => 26],
            ['usuario_id' => 51, 'curriculum_id' => 29],
            ['usuario_id' => 52, 'curriculum_id' => 27],
            ['usuario_id' => 53, 'curriculum_id' => 30],
            ['usuario_id' => 54, 'curriculum_id' => 31],
            ['usuario_id' => 55, 'curriculum_id' => 32],
            ['usuario_id' => 56, 'curriculum_id' => 33],
            ['usuario_id' => 57, 'curriculum_id' => 34],
            ['usuario_id' => 58, 'curriculum_id' => 35],
        ]);
    }
}
