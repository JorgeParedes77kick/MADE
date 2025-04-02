<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRequisitosTable extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('requisitos', function (Blueprint $table) {
            $table->id();
            // $table->string('nombre', 50)->nullable();
            $table->foreignId('ciclo_id')->constrained('ciclos');
            $table->foreignId('ciclo_pre_id')->constrained('ciclos');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('requisitos');
    }
}
