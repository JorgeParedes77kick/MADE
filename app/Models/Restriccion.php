<?php

namespace App\Models;

use App\Helpers\RestriccionHelper;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Restriccion extends Model {
    use HasFactory;

    protected $table = 'restricciones';

    protected $fillable = [
        'nombre',
        'tipo_restriccion_id',
        'valor_restriccion',
        'curriculum_id',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Relación con el tipo de restricción
     */
    public function tipoRestriccion(): BelongsTo {
        return $this->belongsTo(TipoRestriccion::class, 'tipo_restriccion_id');
    }

    /**
     * Relación con el currículum
     */
    public function curriculum(): BelongsTo {
        return $this->belongsTo(Curriculum::class, 'curriculum_id');
    }

    protected static function booted() {
        static::addGlobalScope('withRelations', function (Builder $builder) {
            $builder->with(['tipoRestriccion:id,nombre']);
        });
    }
    protected static function scopeParejas($query) {
        $query->where('tipo_restriccion_id', RestriccionHelper::$PAREJAS);
    }
    protected static function scopeHombres($query) {
        $query->where('tipo_restriccion_id', RestriccionHelper::$MASCULINO);
    }
    protected static function scopeMujeres($query) {
        $query->where('tipo_restriccion_id', RestriccionHelper::$FEMENINO);
    }
}
