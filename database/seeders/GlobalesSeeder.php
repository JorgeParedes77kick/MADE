<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GlobalesSeeder extends Seeder {
    /**
     * Run the database seeds.
     *
     * @return void
     */public function run() {
        DB::table('globales')->insertOrIgnore([
            ['id' => 1, 'nombre' => 'GruposPorUsuario', 'valor' => '3', 'tipo' => 'number'],
            ['id' => 2, 'nombre' => 'InscripcionPorGrupo', 'valor' => '15', 'tipo' => 'number'],
        ]);
    }
}
