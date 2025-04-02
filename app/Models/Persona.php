<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;

class Persona extends Model {
    use HasFactory;

    protected $guarded = [];
    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'id';

    protected $table = 'personas';

    /**
     * Columns used to insert
     * @var string[]
     */
    protected $fillable = [
        'nombre',
        'apellido',
        'tipo_documento_id',
        'dni',
        'fecha_nacimiento',
        'genero_id',
        'estado_civil_id',
        'region_id',
        'ciudad',
        'nacionalidad_id',
        'direccion',
        'telefono',
        'ocupacion',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'fecha_nacimiento' => 'datetime',
    ];

    protected $appends = ['edad'];

    public function getFechaNacimientoAttribute($value) {
        return Carbon::parse($value)->format('Y-m-d');
    }
    public function getEdadAttribute() {
        $fechaNacimiento = Carbon::parse($this->fecha_nacimiento);
        $edad = $fechaNacimiento->diffInYears(Carbon::now());
        return $edad;
    }
    /**
     * Relación con el género
     */
    public function genero(): BelongsTo {
        return $this->belongsTo(Genero::class, 'genero_id');
    }

    /**
     * Relación con el estado civil
     */
    public function estadoCivil(): BelongsTo {
        return $this->belongsTo(EstadoCivil::class, 'estado_civil_id');
    }

    /**
     * Relación con el region
     */
    public function region(): BelongsTo {
        return $this->belongsTo(Region::class, 'region_id');
    }
    /**
     * Relación con el pais
     */
    public function pais(): HasOneThrough {
        return $this->hasOneThrough(
            Pais::class, // Modelo final
            Region::class, // Modelo intermedio
            'id', // Llave foránea en la tabla intermedia (Region)
            'id', // Llave primaria en la tabla final (Pais)
            'region_id', // Llave foránea en el modelo actual (Usuario)
            'pais_id' // Llave foránea en la tabla intermedia (Region)
        );
    }
    // /**
    //  * Relación con la ciudad
    //  */
    // public function ciudad(): BelongsTo
    // {
    //     return $this->belongsTo(Ciudad::class, 'ciudad_id');
    // }

    /**
     * Relación con la nacionalidad
     */
    public function nacionalidad(): BelongsTo {
        return $this->belongsTo(Nacionalidad::class, 'nacionalidad_id');
    }

    /**
     * Relación con el usuario asociado a la persona
     */
    public function user(): HasOne {
        return $this->hasOne(Usuario::class, 'persona_id');
    }
}
