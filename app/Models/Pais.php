<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Pais extends Model {
    use HasFactory;

    protected $table = 'paises';

    protected $fillable = [
        'nombre',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // /**
    //  * Relación con las personas - DEPRECATED
    //  */
    // public function personas(): HasMany
    // {
    //     return $this->hasMany(Persona::class, 'pais_id');
    // }
    /**
     * Relación con las personas
     */
    public function regiones(): HasMany {
        return $this->hasMany(Region::class, 'pais_id');
    }
}
