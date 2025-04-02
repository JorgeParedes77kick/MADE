<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Rol extends Model
{
    use HasFactory;

    protected $table = 'roles';

    /**
     * Columns used to insert
     * @var string[]
     */
    protected $fillable = [
        'nombre',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
    /**
     * Relación con los menus asignados al rol
     */
    public function menus(): BelongsToMany
    {
        return $this->belongsToMany(Menu::class, 'roles_menus', 'rol_id', 'menu_id');
    }
    /**
     * Relación con los permisos asignados al rol
     */
    public function permisos(): BelongsToMany
    {
        return $this->belongsToMany(Permiso::class, 'roles_permisos', 'rol_id', 'permiso_id');
    }

    /**
     * Relación con los usuarios que tienen este rol
     */
    public function usuarios(): BelongsToMany
    {
        return $this->belongsToMany(Usuario::class, 'usuario_roles', 'rol_id', 'usuario_id');
    }
    /**
     * Relación con las inscripciones
     */
    public function inscripciones(): HasMany
    {
        return $this->hasMany(Inscripcion::class, 'rol_id');
    }
}
