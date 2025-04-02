<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMenusTable extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('menus', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 50);
            $table->unsignedBigInteger('menu_padre_id')->nullable();
            $table->text('url_ref');
            $table->integer('orden')->default(90);
            $table->text('icon')->nullable();
            $table->timestamps();

            $table->foreign('menu_padre_id')->references('id')->on('menus');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('menus');
    }
}
