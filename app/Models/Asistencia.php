<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Asistencia extends Model {
    use HasFactory;

    protected $table = 'asistencias';

    protected $fillable = [
        'inscripcion_id',
        'semana_id',
        'estado_asistencia_id',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
    protected $hidden = ['created_at', 'updated_at'];

    /**
     * Relación con la inscripción
     */
    public function inscripcion(): BelongsTo {
        return $this->belongsTo(Inscripcion::class, 'inscripcion_id');
    }

    /**
     * Relación con la semana
     */
    public function semana(): BelongsTo {
        return $this->belongsTo(Semana::class, 'semana_id');
    }

    /**
     * Relación con el estado de asistencia
     */
    public function estadoAsistencia(): BelongsTo {
        return $this->belongsTo(EstadoAsistencia::class, 'estado_asistencia_id');
    }
    public function temporada() {
        return $this->hasOneThrough(Temporada::class, Semana::class,
            'id' /*id semana*/,
            'id' /* id temporada */,
            'semana_id' /*fk asistencia semana */,
            'temporada_id' /* fk semana temporada*/);
    }
    public function grupoPequeno() {
        return $this->hasOneThrough(GrupoPequeno::class, Inscripcion::class,
            'id' /*id semana*/,
            'id' /* id temporada */,
            'inscripcion_id' /*fk asistencia inscripcion */,
            'grupo_pequeno_id' /* fk semana temporada*/);
    }

    public function scopeWhereHasGrupo($q, $grupo_id = null) {
        return $q->whereHas('inscripcion', function ($query) use ($grupo_id) {
            $query->whereColumn('inscripciones.grupo_pequeno_id', 'grupo_pequenos.id');

        });
    }
    public function scopeWhereHasCiclo($q, $ciclo_id) {
        return $q->whereHas('grupoPequeno.ciclo_id', $ciclo_id);
    }
}
