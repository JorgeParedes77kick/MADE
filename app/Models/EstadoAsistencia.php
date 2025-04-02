<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EstadoAsistencia extends Model {
    use HasFactory;

    protected $table = 'estados_asistencias';

    protected $fillable = [
        'estado',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
    protected $hidden = ['created_at', 'updated_at'];

    protected $appends = ['key'];

    public function getKeyAttribute() {
        return (strlen($this->estado) > 0) ? ucfirst($this->estado)[0] : $this->estado;
    }
    /**
     * Relación con las asistencias
     */
    public function asistencias(): HasMany {
        return $this->hasMany(Asistencia::class, 'estado_asistencia_id');
    }
}
