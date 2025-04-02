<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePersonasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('personas', function (Blueprint $table) {
            $table->id();
            // $table->string('correo', 250)->unique();
            // $table->string('password');
            $table->string('nombre', 50);
            // $table->timestamp('email_verified_at')->nullable();
            $table->string('apellido', 50);
            $table->string('dni', 20)->nullable();
            $table->date('fecha_nacimiento')->nullable();
            $table->foreignId('genero_id')->constrained('generos');
            $table->foreignId('estado_civil_id')->constrained('estados_civiles');
            // $table->foreignId('pais_id')->constrained('paises');
            $table->unsignedBigInteger('region_id')->nullable();
            $table->string('ciudad', 100)->nullable();
            $table->foreignId('nacionalidad_id')->constrained('nacionalidades');
            $table->string('direccion', 255)->nullable();
            $table->string('codigo_postal', 100)->nullable();
            $table->string('telefono', 20)->nullable();
            $table->string('fotografia')->nullable();
            $table->string('ocupacion')->nullable();
            $table->string('informacion_adicional', 250)->nullable();
            $table->timestamps();

            $table->foreign('region_id')->references('id')->on('regiones');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('personas');
    }
}
