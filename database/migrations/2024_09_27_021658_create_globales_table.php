<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGlobalesTable extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('globales', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 50);
            $table->text('valor');
            $table->enum('tipo', ['string', 'number', 'boolean', 'date'])->default('string');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('globales');

    }
}
