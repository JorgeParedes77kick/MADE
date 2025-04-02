<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnTemporadas extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void {
        if (!Schema::hasColumn('temporadas', 'fecha_extension')) {
            Schema::table('temporadas', function (Blueprint $table) {
                $table->date('fecha_extension')->after('fecha_cierre')->nullable()->default(null);
            });
        }
        if (!Schema::hasColumn('curriculums', 'activo_extension')) {
            Schema::table('curriculums', function (Blueprint $table) {
                $table->boolean('activo_extension')->after('activo')->default(false);
            });
        }
        if (!Schema::hasColumn('grupo_pequenos', 'activo_extension')) {
            Schema::table('grupo_pequenos', function (Blueprint $table) {
                $table->boolean('activo_extension')->after('activo_inscripcion')->default(false);
            });
        }
        if (!Schema::hasColumn('semanas', 'es_extension')) {
            Schema::table('semanas', function (Blueprint $table) {
                $table->boolean('es_extension')->after('fecha_fin')->default(false);
            });
        }

        // if (!Schema::hasColumn('asistencias', 'semana')) {
        //     Schema::table('asistencias', function (Blueprint $table) {
        //         $table->date('semana')->after('id')->nullable();
        //         $table->unsignedBigInteger('temporada_id')->after('semana')->nullable();
        //         $table->foreign('temporada_id')->references('id')->on('temporadas')->onDelete('set null');
        //     });
        // }

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void {
        if (Schema::hasColumn('curriculums', 'activo_extension')) {
            Schema::table('curriculums', function (Blueprint $table) {
                $table->dropColumn('activo_extension');
            });
        }

        if (Schema::hasColumn('grupo_pequenos', 'activo_extension')) {
            Schema::table('grupo_pequenos', function (Blueprint $table) {
                $table->dropColumn('activo_extension');
            });
        }

        if (Schema::hasColumn('temporadas', 'fecha_extension')) {
            Schema::table('temporadas', function (Blueprint $table) {
                $table->dropColumn('fecha_extension');
            });
        }
        if (Schema::hasColumn('semanas', 'es_extension')) {
            Schema::table('semanas', function (Blueprint $table) {
                $table->dropColumn('es_extension');
            });
        }
        // if (Schema::hasColumn('asistencias', 'semana')) {
        //     Schema::table('asistencias', function (Blueprint $table) {
        //         $table->dropForeign('asistencias_temporada_id_foreign');
        //         $table->dropColumn('temporada_id');
        //         $table->dropColumn('semana');
        //     });
        // }

    }
}
