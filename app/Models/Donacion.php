<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Donacion extends Model
{
    use HasFactory;

    protected $table = 'donations';

    protected $fillable = [
        'monto',
        'motivo',
        'comprobante',
        'grupo_pequeno_id',
        'temporada_id',
        'usuario_id',
        'rol_id',
    ];

    /**
     * Relación con el grupo pequeño
     */
    public function grupoPequeno(): BelongsTo {
        return $this->belongsTo(GrupoPequeno::class, 'grupo_pequeno_id');
    }

    /**
     * Relación con la temporada
     */
    public function temporada(): BelongsTo {
        return $this->belongsTo(Temporada::class, 'temporada_id');
    }

    /**
     * Relación con el usuario
     */
    public function usuario(): BelongsTo {
        return $this->belongsTo(Usuario::class, 'usuario_id');
    }

    /**
     * Relación con el usuario
     */
    public function rol(): BelongsTo {
        return $this->belongsTo(Rol::class, 'rol_id');
    }
}
