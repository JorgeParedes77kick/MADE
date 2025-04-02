<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\Pivot;

class AdicionalInscripcion extends Pivot {
    use HasFactory;

    protected $table = 'adicional_inscripciones';

    protected $fillable = [
        'adicional_id',
        'inscripcion_id',
        'value',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

}
