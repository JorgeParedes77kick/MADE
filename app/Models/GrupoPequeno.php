<?php
namespace App\Models;

use App\Helpers\InscripcionHelper;
use App\Helpers\RolHelper;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class GrupoPequeno extends Model {
    use HasFactory;

    protected $table = 'grupo_pequenos';

    protected $fillable = [
        'ciclo_id',
        'dia_curso',
        'hora_inicio',
        'hora_fin',
        'activo_inscripcion',
        'temporada_id',
        'info_adicional',
        'nombre_curso',
    ];

    protected $casts = [
        'created_at'         => 'datetime',
        'updated_at'         => 'datetime',
        // 'hora_inicio' => 'time',
        // 'hora_fin' => 'time',
        'activo_inscripcion' => 'boolean',
    ];
    protected $appends = ['hora', 'horaInicioFormat', 'horaFinFormat', 'idCrypt'];

    public function getIdCryptAttribute() {
        return base64_encode($this->id);
    }
    public function getHoraInicioFormatAttribute() {
        // Verifica si 'hora_inicio' no es nulo y está en el formato correcto
        if ($this->hora_inicio && Carbon::hasFormat($this->hora_inicio, 'H:i:s')) {
            return Carbon::createFromFormat('H:i:s', $this->hora_inicio)->format('H:i') . ' hrs';
        }

        return null; // O puedes devolver un valor predeterminado si es necesario
    }

    // Accessor para hora_fin formateada
    public function getHoraFinFormatAttribute() {
        // Verifica si 'hora_fin' no es nulo y está en el formato correcto
        if ($this->hora_fin && Carbon::hasFormat($this->hora_fin, 'H:i:s')) {
            return Carbon::createFromFormat('H:i:s', $this->hora_fin)->format('H:i') . ' hrs';
        }

        return null; // O puedes devolver un valor predeterminado si es necesario
    }
    public function getHoraAttribute() {
        if ($this->hora_inicio && Carbon::hasFormat($this->hora_inicio, 'H:i:s') && $this->hora_fin && Carbon::hasFormat($this->hora_fin, 'H:i:s')) {
            return Carbon::createFromFormat('H:i:s', $this->hora_inicio)->format('H:i') . ' hrs ' . Carbon::createFromFormat('H:i:s', $this->hora_fin)->format('H:i') . ' hrs';
        }
        return null;
    }
    /**
     * Relación con el ciclo
     */
    public function ciclo(): BelongsTo {
        return $this->belongsTo(Ciclo::class, 'ciclo_id');
    }

    /**
     * Relación con la temporada
     */
    public function temporada(): BelongsTo {
        return $this->belongsTo(Temporada::class, 'temporada_id');
    }

    /**
     * Relación con las inscripciones
     */
    public function inscripciones(): HasMany {
        return $this->hasMany(Inscripcion::class, 'grupo_pequeno_id');
    }
    public function inscripcionesLideres(): HasMany {
        return $this->hasMany(Inscripcion::class, 'grupo_pequeno_id')->where('rol_id', RolHelper::$LIDER);
    }
    public function inscripcionesMonitores(): HasMany {
        return $this->hasMany(Inscripcion::class, 'grupo_pequeno_id')->where('rol_id', RolHelper::$MONITOR);
    }
    public function inscripcionesAlumnos(): HasMany {
        return $this->hasMany(Inscripcion::class, 'grupo_pequeno_id')->where('rol_id', RolHelper::$ALUMNO);
    }
    // Relación para los líderes (rol_id = 3)
    public function lideres(): HasManyThrough {
        // return $this->hasMany(Inscripcion::class, 'grupo_pequeno_id')->where('rol_id', RolHelper::$LIDER);
        return $this->hasManyThrough(
            Usuario::class, Inscripcion::class, 'grupo_pequeno_id', 'id', 'id', 'usuario_id')
            ->where('inscripciones.rol_id', RolHelper::$LIDER);
    }

    public function monitores(): HasManyThrough {
        return $this->hasManyThrough(
            Usuario::class, Inscripcion::class, 'grupo_pequeno_id', 'id', 'id', 'usuario_id')
            ->where('inscripciones.rol_id', RolHelper::$MONITOR);
    }
    public function alumnos(): HasManyThrough {
        return $this->hasManyThrough(
            Usuario::class, Inscripcion::class, 'grupo_pequeno_id', 'id', 'id', 'usuario_id')
            ->where('inscripciones.rol_id', RolHelper::$ALUMNO);
    }
    public function alumnosAprobados(): HasManyThrough {
        return $this->hasManyThrough(
            Usuario::class, Inscripcion::class, 'grupo_pequeno_id', 'id', 'id', 'usuario_id')
            ->where('inscripciones.rol_id', RolHelper::$ALUMNO)
            ->where('inscripciones.estado_inscripcion_id', InscripcionHelper::$APROBADO);
    }
    public function alumnosReprobados(): HasManyThrough {
        return $this->hasManyThrough(
            Usuario::class, Inscripcion::class, 'grupo_pequeno_id', 'id', 'id', 'usuario_id')
            ->where('inscripciones.rol_id', RolHelper::$ALUMNO)
            ->where('inscripciones.estado_inscripcion_id', InscripcionHelper::$REPROBADO);
    }
    public function alumnosInscritos(): HasManyThrough {
        return $this->hasManyThrough(
            Usuario::class, Inscripcion::class, 'grupo_pequeno_id', 'id', 'id', 'usuario_id')
            ->where('inscripciones.rol_id', RolHelper::$ALUMNO)
            ->where('inscripciones.estado_inscripcion_id', InscripcionHelper::$INSCRITO);
    }
    public function alumnosNoParticipa(): HasManyThrough {
        return $this->hasManyThrough(
            Usuario::class, Inscripcion::class, 'grupo_pequeno_id', 'id', 'id', 'usuario_id')
            ->where('inscripciones.rol_id', RolHelper::$ALUMNO)
            ->where('inscripciones.estado_inscripcion_id', InscripcionHelper::$NO_PARTICIPO);
    }

    // public function semanas(): HasManyThrough {
    //     return $this->hasManyThrough(Semana::class, Temporada::class, 'temporada_id', 'temporada_id', 'id', 'id');
    // }
    public function semanas(): HasManyThrough {
        return $this->hasManyThrough(Semana::class, Temporada::class, 'id', 'temporada_id', 'temporada_id', 'id');
    }

    public function scopeActivo($query) {
        return $query->where('activo_inscripcion', true);
    }

}
