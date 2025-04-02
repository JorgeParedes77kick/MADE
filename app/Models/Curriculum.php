<?php

namespace App\Models;

use App\Helpers\RestriccionHelper;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Curriculum extends Model {
    use HasFactory;

    protected $table = 'curriculums';

    protected $fillable = [
        'nombre',
        'libro',
        'descripcion',
        'cantidad_clases',
        'cantidad_cupos', //por Grupo ?
        'imagen',
        'imagen_landing',
        'activo',
    ];

    protected $casts = [
        'activo' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    protected $appends = ['idCrypt'];

    public function getIdCryptAttribute() {
        return base64_encode($this->id);
    }
    /**
     * Relación con las restricciones
     */
    public function restricciones(): HasMany {
        return $this->hasMany(Restriccion::class, 'curriculum_id');
    }

    /**
     * Relación con los ciclos
     */
    public function ciclos(): HasMany {
        return $this->hasMany(Ciclo::class, 'curriculum_id');
    }

    /**
     * Relación con los adicionales
     */
    public function adicionales(): HasMany {
        return $this->hasMany(Adicional::class, 'curriculum_id');
    }
    /**
     * Relación con los usuarios
     */
    public function usuarios(): BelongsToMany {
        return $this->belongsToMany(Usuario::class, 'usuario_curriculums');
    }

    public function scopeActivo($query) {
        return $query->where('activo', true);
    }

    public function scopeValidacion($query, $persona) {
        return $query->whereDoesntHave('restricciones', function ($query) use ($persona) {
            $query->where(function ($q) use ($persona) {
                // Agregar condiciones para restricciones de edad mínima
                $q->where(function ($subQuery) use ($persona) {
                    $subQuery->where('tipo_restriccion_id', RestriccionHelper::$EDAD_MINIMA)
                        ->where('valor_restriccion', '>', $persona->edad);
                })
                // Agregar condiciones para restricciones de edad máxima
                    ->orWhere(function ($subQuery) use ($persona) {
                        $subQuery->where('tipo_restriccion_id', RestriccionHelper::$EDAD_MAXIMA)
                            ->where('valor_restriccion', '<', $persona->edad);
                    })
                // Agregar condiciones para restricciones de parejas
                    ->orWhere(function ($subQuery) use ($persona) {
                        $subQuery->where('tipo_restriccion_id', RestriccionHelper::$PAREJAS);
                        if (!in_array($persona->estado_civil_id, [2, 5, 6])) {
                            $subQuery->whereRaw('1 = 1'); // True si cumple la restricción
                        } else {
                            $subQuery->whereRaw('1 = 0'); // False si no la cumple
                        }
                    })
                // Agregar condiciones para restricciones de género masculino
                    ->orWhere(function ($subQuery) use ($persona) {
                        $subQuery->where('tipo_restriccion_id', RestriccionHelper::$MASCULINO);
                        if ($persona->genero_id != 1) {
                            $subQuery->whereRaw('1 = 1'); // True si cumple la restricción
                        } else {
                            $subQuery->whereRaw('1 = 0'); // False si no la cumple
                        }
                    })
                // Agregar condiciones para restricciones de género femenino
                    ->orWhere(function ($subQuery) use ($persona) {
                        $subQuery->where('tipo_restriccion_id', RestriccionHelper::$FEMENINO);
                        if ($persona->genero_id != 2) {
                            $subQuery->whereRaw('1 = 1'); // True si cumple la restricción
                        } else {
                            $subQuery->whereRaw('1 = 0'); // False si no la cumple
                        }
                    });
            });
        });
    }

    // $curriculums = Curriculum::where($basic)->whereHas('restricciones')->get();
    // $curriculumsCorrect = collect();
    // $curriculums->each(function ($curriculum) use ($persona, $curriculumsCorrect) {
    //     $add = true;
    //     $curriculum->restricciones->each(function ($restriccion) use ($persona, &$add) {
    //         switch ($restriccion->tipo_restriccion_id) {
    //         case RestriccionHelper::$EDAD_MINIMA:
    //             if ($restriccion->valor_restriccion > $persona->edad) {$add = false;}
    //             break;
    //         case RestriccionHelper::$EDAD_MAXIMA:
    //             if ($restriccion->valor_restriccion < $persona->edad) {$add = false;}
    //             break;
    //         case RestriccionHelper::$PAREJAS:
    //             if (!in_array($persona->estado_civil_id, [2, 5, 6])) {$add = false;}
    //             break;
    //         case RestriccionHelper::$MASCULINO:
    //             if ($persona->genero_id != 1) {$add = false;}
    //             break;
    //         case RestriccionHelper::$FEMENINO:
    //             if ($persona->genero_id != 2) {$add = false;}
    //             break;
    //         default:
    //             break;
    //         }
    //     });
    //     if ($add) {$curriculumsCorrect->add($curriculum);}
    // });
}
