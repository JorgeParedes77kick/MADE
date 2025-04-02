<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInscripcionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('inscripciones', function (Blueprint $table) {
            $table->id();
            // $table->date('fecha_inscripcion');
            $table->foreignId('usuario_id')->constrained('usuarios');
            $table->foreignId('rol_id')->constrained('roles');
            $table->foreignId('grupo_pequeno_id')->constrained('grupo_pequenos');
            $table->foreignId('estado_inscripcion_id')->constrained('estados_inscripciones');
            $table->string('info_adicional')->nullable();
            $table->timestamps();
            $table->unique(['usuario_id', 'rol_id', 'grupo_pequeno_id'], 'inscripciones_unique_usuario_rol_grupo');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('inscripciones');
    }
}
