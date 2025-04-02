<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MenuSeeder extends Seeder {
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run() {
        // DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        // DB::table('menus')->truncate(); // Reinicia los IDs
        DB::table('menus')->insertOrIgnore([
            ['id' => 1, 'nombre' => 'Home', 'menu_padre_id' => null, 'url_ref' => '/home', 'orden' => 0],
            ['id' => 2, 'nombre' => 'Dashboard', 'menu_padre_id' => null, 'url_ref' => '/dashboard', 'orden' => 0],
            ['id' => 3, 'nombre' => 'Temporadas', 'menu_padre_id' => null, 'url_ref' => '/temporadas', 'orden' => 0],
            ['id' => 4, 'nombre' => 'Administrativo', 'menu_padre_id' => null, 'url_ref' => '#', 'orden' => 0],
            ['id' => 5, 'nombre' => 'Roles', 'menu_padre_id' => 4, 'url_ref' => '/roles', 'orden' => 0],
            ['id' => 6, 'nombre' => 'Usuarios equipo', 'menu_padre_id' => 4, 'url_ref' => '/usuarios-equipo', 'orden' => 0],
            ['id' => 7, 'nombre' => 'Menu', 'menu_padre_id' => 4, 'url_ref' => '/menu', 'orden' => 0],
            ['id' => 8, 'nombre' => 'Asocición de Roles Menus', 'menu_padre_id' => 4, 'url_ref' => '/rol-menu', 'orden' => 0],
            ['id' => 9, 'nombre' => 'Grupos pequeños', 'menu_padre_id' => null, 'url_ref' => '#', 'orden' => 0],
            ['id' => 10, 'nombre' => 'Historico', 'menu_padre_id' => 9, 'url_ref' => '/grupos-pequenos', 'orden' => 0],
            ['id' => 11, 'nombre' => 'De la temporada', 'menu_padre_id' => 9, 'url_ref' => '/grupos-pequenos/horario', 'orden' => 0],
            ['id' => 12, 'nombre' => 'Estados', 'menu_padre_id' => null, 'url_ref' => '#', 'orden' => 0],
            ['id' => 13, 'nombre' => 'Estados asistencia', 'menu_padre_id' => 12, 'url_ref' => '/estados-asistencia', 'orden' => 0],
            ['id' => 14, 'nombre' => 'Estados inscripcion', 'menu_padre_id' => 12, 'url_ref' => '/estados-inscripcion', 'orden' => 0],
            ['id' => 15, 'nombre' => 'Gestión Curriculums', 'menu_padre_id' => null, 'url_ref' => '#', 'orden' => 0],
            ['id' => 16, 'nombre' => 'Curriculums', 'menu_padre_id' => 15, 'url_ref' => '/curriculums', 'orden' => 0],
            ['id' => 17, 'nombre' => 'Ciclos', 'menu_padre_id' => 15, 'url_ref' => '/ciclos', 'orden' => 0],
            ['id' => 18, 'nombre' => 'Restricciones', 'menu_padre_id' => 15, 'url_ref' => '/restricciones', 'orden' => 0],
            ['id' => 19, 'nombre' => 'Adicionales', 'menu_padre_id' => 15, 'url_ref' => '/adicionales-curriculum', 'orden' => 0],
            ['id' => 20, 'nombre' => 'Recursos', 'menu_padre_id' => 15, 'url_ref' => '/recursos', 'orden' => 0],
            ['id' => 21, 'nombre' => 'Asistencias', 'menu_padre_id' => null, 'url_ref' => '/asistencias', 'orden' => 0],
            ['id' => 22, 'nombre' => 'Inscripción Interna', 'menu_padre_id' => null, 'url_ref' => '/inscripcion', 'orden' => 0],
            ['id' => 23, 'nombre' => 'Mis recursos', 'menu_padre_id' => null, 'url_ref' => '/mis-recursos', 'orden' => 0],
            ['id' => 24, 'nombre' => 'Mis salones', 'menu_padre_id' => null, 'url_ref' => '/mis-salones', 'orden' => 0],
        ]);

        // DB::statement('SET FOREIGN_KEY_CHECKS=1;');

    }

}
