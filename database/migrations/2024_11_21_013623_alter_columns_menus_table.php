<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterColumnsMenusTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn('menus', 'menu_padre_id')) {
          Schema::table('menus', function (Blueprint $table) {
            $table->unsignedBigInteger('menu_padre_id')->nullable()->default(null);
            $table->unsignedBigInteger('orden')->nullable()->default(null);
          });
        }

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {}
}
