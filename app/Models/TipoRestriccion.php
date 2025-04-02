<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TipoRestriccion extends Model
{
    use HasFactory;
    protected $table = 'tipo_restricciones';

    protected $fillable = [
        'nombre',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Relación con las restricciones
     */
    public function restricciones(): HasMany
    {
        return $this->hasMany(Restriccion::class, 'tipo_restriccion_id');
    }
}
