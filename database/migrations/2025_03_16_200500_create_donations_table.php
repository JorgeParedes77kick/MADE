<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDonationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('donations', function (Blueprint $table) {
            $table->id();
            $table->decimal('monto',10,2);
            $table->string('motivo',250);
            $table->longText('comprobante');
            $table->foreignId('grupo_pequeno_id')->constrained('grupo_pequenos');
            $table->foreignId('temporada_id')->constrained('temporadas');
            $table->foreignId('usuario_id')->constrained('usuarios');
            $table->foreignId('rol_id')->constrained('roles');
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
        Schema::dropIfExists('donations');
    }
}
