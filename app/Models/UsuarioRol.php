<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Pivot;

class UsuarioRol extends Pivot
{
    use HasFactory;

    protected $table = 'usuario_roles';

    protected $fillable = [
        'usuario_id',
        'rol_id',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
}
