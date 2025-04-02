<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnsPersonasTable extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        if (!Schema::hasColumn('personas', 'tipo_documento_id')) {
            Schema::table('personas', function (Blueprint $table) {
                $table->unsignedBigInteger('tipo_documento_id')->after('apellido')->nullable()->default(null);
                $table->foreign('tipo_documento_id')->references('id')->on('tipo_documentos');

            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table('personas', function (Blueprint $table) {
            // Verificar si la columna existe antes de intentar eliminar la clave foránea
            if (Schema::hasColumn('personas', 'tipo_documento_id')) {
                // Eliminar la clave foránea
                $table->dropForeign(['tipo_documento_id']);

                // Eliminar la columna
                $table->dropColumn('tipo_documento_id');
            }
        });
    }
}
