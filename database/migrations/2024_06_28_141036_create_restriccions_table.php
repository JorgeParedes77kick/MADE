<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRestriccionsTable extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('restricciones', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 100)->nullable();
            $table->foreignId('tipo_restriccion_id')->constrained('tipo_restricciones');
            $table->string('valor_restriccion', 50)->default('');
            $table->foreignId('curriculum_id')->constrained('curriculums');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('restricciones');
    }
}
