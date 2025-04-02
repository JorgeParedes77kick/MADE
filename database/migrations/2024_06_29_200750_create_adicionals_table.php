<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAdicionalsTable extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('adicionales', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 50);
            $table->foreignId('curriculum_id')->constrained('curriculums');
            $table->enum('type_value', ['string', 'number', 'boolean', 'none'])->default('none');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('adicionales');
    }
}
