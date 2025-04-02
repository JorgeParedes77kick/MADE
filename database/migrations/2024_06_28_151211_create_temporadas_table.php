<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTemporadasTable extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('temporadas', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 250);
            $table->string('prefijo', 100)->unique();
            $table->string('titulo', 250)->default('')->nullable();
            $table->date('fecha_inicio');
            $table->date('fecha_cierre');
            $table->date('inscripcion_inicio')->nullable();
            $table->date('inscripcion_cierre')->nullable();
            $table->boolean('activo')->default(false);
            $table->boolean('activo_inscripcion')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('temporadas');
    }
}
