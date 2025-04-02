<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Region extends Model {
    use HasFactory;

    protected $table = 'regiones';

    protected $fillable = [
        'nombre',
        'pais_id',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Relación con las personas
     */
    public function personas(): HasMany {
        return $this->hasMany(Persona::class, 'region_id');
    }
    public function pais(): BelongsTo {
        return $this->belongsTo(Pais::class, 'pais_id');
    }
}
