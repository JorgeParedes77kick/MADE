<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Nacionalidad extends Model
{
    use HasFactory;

    protected $table = 'nacionalidades';

    protected $fillable = [
        'nombre',
        'iso',
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
        return $this->hasMany(Persona::class, 'nacionalidad_id');
    }
}
