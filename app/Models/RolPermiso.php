<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Pivot;

class RolPermiso extends Pivot
{
    use HasFactory;


    protected $table = 'roles_permisos';

    protected $fillable = [
        'rol_id',
        'permiso_id',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
}
