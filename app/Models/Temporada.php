<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Temporada extends Model {
    use HasFactory;

    protected $table = 'temporadas';

    protected $fillable = [
        'nombre',
        'prefijo',
        'titulo',
        'fecha_inicio',
        'fecha_cierre',
        'fecha_extension',
        'inscripcion_inicio',
        'inscripcion_cierre',
        'activo',
        'activo_inscripcion',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        // 'fecha_inicio' => 'string',
        // 'fecha_cierre' => 'string',
        // 'fecha_extension => 'string',
        // 'inscripcion_inicio' => 'string',
        // 'inscripcion_cierre' => 'string',

        'activo' => 'boolean',
        'activo_inscripcion' => 'boolean',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];
    protected $attributes = [
        'titulo' => '',
    ];
    protected $appends = ['semanas', 'fecha_inicio_w', 'fecha_cierre_w', 'fecha_extension_w'];
    public function getFechaInicioFormatAttribute() {
        if ($this->fecha_inicio && Carbon::hasFormat($this->fecha_inicio, 'Y-m-d')) {
            return Carbon::createFromFormat('Y-m-d', $this->fecha_inicio)->format('Y-m-d');
        }
        return null; // O puedes devolver un valor predeterminado si es necesario
    }
    public function getFechaCierreFormatAttribute() {
        if ($this->fecha_cierre && Carbon::hasFormat($this->fecha_cierre, 'Y-m-d')) {
            return Carbon::createFromFormat('Y-m-d', $this->fecha_cierre)->format('Y-m-d');
        }
        return null; // O puedes devolver un valor predeterminado si es necesario
    }
    public function getFechaExtensionFormatAttribute() {
        if ($this->fecha_extension && Carbon::hasFormat($this->fecha_extension, 'Y-m-d')) {
            return Carbon::createFromFormat('Y-m-d', $this->fecha_extension)->format('Y-m-d');
        }
        return null; // O puedes devolver un valor predeterminado si es necesario
    }
    public function getFechaInicioWAttribute() {
        if ($this->fecha_inicio && Carbon::hasFormat($this->fecha_inicio, 'Y-m-d')) {
            return Carbon::createFromFormat('Y-m-d', $this->fecha_inicio)->format('Y-\WW');
        }
        return null; // O puedes devolver un valor predeterminado si es necesario
    }
    public function getFechaCierreWAttribute() {
        if ($this->fecha_cierre && Carbon::hasFormat($this->fecha_cierre, 'Y-m-d')) {
            return Carbon::createFromFormat('Y-m-d', $this->fecha_cierre)->format('Y-\WW');
        }
        return null; // O puedes devolver un valor predeterminado si es necesario
    }
    public function getFechaExtensionWAttribute() {
        if ($this->fecha_extension && Carbon::hasFormat($this->fecha_extension, 'Y-m-d')) {
            return Carbon::createFromFormat('Y-m-d', $this->fecha_extension)->format('Y-\WW');
        }
        return null; // O puedes devolver un valor predeterminado si es necesario
    }

    public function getSemanasAttribute() {
        $fecha_inicio = Carbon::parse($this->fecha_inicio);
        $fecha_cierre = Carbon::parse($this->fecha_cierre);
        $dias = $fecha_inicio->diffInDays($fecha_cierre);
        $semanas = $dias / 7;
        return ceil($semanas);
    }

    /**
     * Relación con los grupos pequeños
     */
    public function gruposPequenos(): HasMany {
        return $this->hasMany(GrupoPequeno::class, 'temporada_id');
    }

    /**
     * Relación con las semanas
     */
    public function semanas(): HasMany {
        return $this->hasMany(Semana::class, 'temporada_id');
    }

    public function asistencias() {
        return $this->hasManyThrough(Asistencia::class, Semana::class, 'temporada_id', 'semana_id', 'id', 'id');
    }

    public function scopeActivo($q) {
        return $q->where('activo', true);
    }
    public function scopeEnInscripcion($q) {
        return $q->where('activo_inscripcion', true);
    }
}
