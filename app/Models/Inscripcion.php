<?php
// app/Models/Inscripcion.php

namespace App\Models;

use App\Helpers\AsistenciaHelper;
use App\Helpers\RolHelper;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Inscripcion extends Model {
    use HasFactory;

    protected $table = 'inscripciones';

    protected $fillable = [
        // 'fecha_inscripcion',
        'usuario_id',
        'rol_id',
        'grupo_pequeno_id',
        'estado_inscripcion_id',
        'info_adicional',

    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        // 'fecha_inscripcion' => 'date',
    ];
    protected $hidden = ['created_at', 'updated_at'];

    protected $appends = ['idCrypt'];

    public function getIdCryptAttribute() {
        return base64_encode($this->id);
    }
    /**
     * Relación con el usuario
     */
    public function usuario(): BelongsTo {
        return $this->belongsTo(Usuario::class, 'usuario_id');
    }

    /**
     * Relación con el rol
     */
    public function rol(): BelongsTo {
        return $this->belongsTo(Rol::class, 'rol_id');
    }

    /**
     * Relación con el grupo pequeño
     */
    public function grupoPequeno(): BelongsTo {
        return $this->belongsTo(GrupoPequeno::class, 'grupo_pequeno_id');
    }

    /**
     * Relación con el estado de inscripción
     */
    public function estadoInscripcion(): BelongsTo {
        return $this->belongsTo(EstadoInscripcion::class, 'estado_inscripcion_id');
    }

    /**
     * Relación con las asistencias
     */
    public function asistencias(): HasMany {
        return $this->hasMany(Asistencia::class, 'inscripcion_id');
    }

    public function semanas() {
        return $this->hasManyThrough(
            Semana::class, Asistencia::class, 'inscripcion_id', 'id', 'id', 'semana_id');
    }
    public function semanasInscritos() {
        return $this->hasManyThrough(
            Semana::class, Asistencia::class, 'inscripcion_id', 'id', 'id', 'semana_id')
            ->where('asistencias.estado_asistencia_id', AsistenciaHelper::$INSCRITO);
    }
    public function semanasPresentes() {
        return $this->hasManyThrough(
            Semana::class, Asistencia::class, 'inscripcion_id', 'id', 'id', 'semana_id')
            ->where('asistencias.estado_asistencia_id', AsistenciaHelper::$PRESENTE);
    }

    public function semanasAusentes() {
        return $this->hasManyThrough(
            Semana::class, Asistencia::class, 'inscripcion_id', 'id', 'id', 'semana_id')
            ->where('asistencias.estado_asistencia_id', AsistenciaHelper::$AUSENTE);
    }
    public function semanasRecuperados() {
        return $this->hasManyThrough(
            Semana::class, Asistencia::class, 'inscripcion_id', 'id', 'id', 'semana_id')
            ->where('asistencias.estado_asistencia_id', AsistenciaHelper::$RECUPERADO);
    }
    public function semanasNoaplica() {
        return $this->hasManyThrough(
            Semana::class, Asistencia::class, 'inscripcion_id', 'id', 'id', 'semana_id')
            ->where('asistencias.estado_asistencia_id', AsistenciaHelper::$NO_APLICA);
    }
    /**
     * Relación con los adicionales
     */
    public function adicionales(): BelongsToMany {
        return $this->belongsToMany(Adicional::class, 'adicional_inscripciones');
    }
    public function scopeAlumno($q) {
        return $q->where('rol_id', RolHelper::$ALUMNO);
    }
    public function scopeLider($q) {
        return $q->where('rol_id', RolHelper::$LIDER);
    }
    public function scopeMonitor($q) {
        return $q->where('rol_id', RolHelper::$MONITOR);
    }

}
