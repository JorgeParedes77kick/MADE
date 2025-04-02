<?php

use Database\Seeders\GlobalesSeeder;
use Database\Seeders\MenuSeeder;
use Database\Seeders\RolesMenusSeeder;
use Database\Seeders\UsuarioCurriculumSeeder;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder {
    public function run() {
        $this->call(GlobalesSeeder::class);
        $this->call(MenuSeeder::class);
        $this->call(RolesMenusSeeder::class);
        $this->call(UsuarioCurriculumSeeder::class);
        // $this->call(EstadosTableSeeder::class);
        // $this->call(NacionalidadesSeeder::class);
        // $this->call(GrupospequenosTableSeeder::class);
        // $this->call(CiclosTableSeeder::class);
        // $this->call(AdminsTableSeeder::class);
        // $this->call(CoordinadorsTableSeeder::class);
        // $this->call(MonitorTableSeeder::class);
        // $this->call(LiderTableSeeder::class);
        //$this->call(UserTableSeeder::class);
        // $this->call(TemporadaTableSeeder::class);
        // $this->call(GpequenoperiodoTableSeeder::class);
        // $this->call(GrupopequenoLiderTableSeeder::class);
        // $this->call(RecursosTableSeeder::class);
    }
}
