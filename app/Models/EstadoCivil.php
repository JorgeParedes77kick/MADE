<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EstadoCivil extends Model
{
    use HasFactory;

    protected $table = 'estados_civiles';

    protected $fillable = [
        'estado',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Relación con las personas
     */
    public function personas(): HasMany
    {
        return $this->hasMany(Persona::class, 'estado_civil_id');
    }
}
