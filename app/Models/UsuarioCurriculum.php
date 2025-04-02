<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Pivot;

class UsuarioCurriculum extends Pivot
{
    use HasFactory;

    protected $table = 'usuario_curriculums';

    protected $fillable = [
        'usuario_id',
        'curriculum_id',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
}
