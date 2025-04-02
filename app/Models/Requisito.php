<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Requisito extends Model {
    use HasFactory;

    protected $table = 'requisitos';

    protected $fillable = [
        'ciclo_id',
        'ciclo_pre_id',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Relación con el ciclo pre-requisito
     */
    public function cicloPre(): BelongsTo {
        return $this->belongsTo(Ciclo::class, 'ciclo_pre_id');
    }

    public function ciclo(): BelongsTo {
        return $this->belongsTo(Ciclo::class, 'ciclo_id');
    }
}
