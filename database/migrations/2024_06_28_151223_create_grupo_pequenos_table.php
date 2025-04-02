<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGrupoPequenosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('grupo_pequenos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('temporada_id')->constrained('temporadas');
            $table->foreignId('ciclo_id')->constrained('ciclos');
            $table->string('nombre_curso', 100)->nullable();
            $table->enum('dia_curso', ['lunes', 'martes', 'miércoles', 'jueves', 'viernes', 'sábado', 'domingo', 'none'])->default('none');
            $table->time('hora_inicio')->nullable();
            $table->time('hora_fin')->nullable();
            $table->boolean('activo_inscripcion')->default(true);
            $table->string('info_adicional')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('grupo_pequenos');
    }
}
