<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TipoDocumento extends Model
{
    use HasFactory;
    protected $table = 'tipo_documentos';

    protected $fillable = [
      'nombre',
    ];

    protected $casts = [
      'created_at' => 'datetime',
      'updated_at' => 'datetime',
    ];
}
