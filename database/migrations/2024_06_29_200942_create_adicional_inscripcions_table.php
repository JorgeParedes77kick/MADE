<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAdicionalInscripcionsTable extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('adicional_inscripciones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('adicional_id')->constrained('adicionales');
            $table->foreignId('inscripcion_id')->constrained('inscripciones');
            $table->string('value', 250)->default('')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('adicional_inscripciones');
    }
}
