<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Recurso extends Model
{
    use HasFactory;

    protected $table = 'recursos';

    protected $fillable = [
        'nombre',
        'link_lectura',
        'link_escritura',
        'clase',
        'ciclo_id',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Relación con el ciclo
     */
    public function ciclo(): BelongsTo
    {
        return $this->belongsTo(Ciclo::class, 'ciclo_id');
    }
}
