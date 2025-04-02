<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Menu extends Model
{
    use HasFactory;

    protected $table = 'menus';

  /**
   * Columns used to insert
   * @var string[]
   */
    protected $fillable = [
        'menu_padre_id',
        'nombre',
        'url_ref',
        'icon',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Relación con el menu padre
     */
    public function menuPadre(): BelongsTo
    {
        return $this->belongsTo(Menu::class, 'menu_padre_id');
    }

    /**
     * Relación con los menus hijos
     */
    public function menusHijos(): HasMany
    {
        return $this->hasMany(Menu::class, 'menu_padre_id');
    }
    /**
     * Relación con los roles que tienen acceso al menu
     */
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Rol::class, 'roles_menus', 'menu_id', 'rol_id');
    }

    public static function getMenu($front = false){
      $menu = new Menu();
      $padres = $menu->getMenuPadres($front);
      $menuAll = [];
      foreach($padres as $root){
        if($root['menu_padre_id'] == null){
          $item = [array_merge($root, ['submenu' => $menu->getMenuHijos($padres, $root)])];
          $menuAll = array_merge($menuAll, $item);
        }

      }
      return $menuAll;
    }

    private function getMenuPadres($front){
      if($front){
        return $this->whereHas('roles', function($query){
          $query->where('roles.id', session('rol_id'))->orderBy('menus.id');
        })->orderBy('menus.orden')
          ->orderBy('menus.id')
          ->get()
          ->toArray();
      }else{
        return $this->orderBy('menus.orden')
          ->orderBy('menus.id')
          ->get()
          ->toArray();
      }
    }

    private function getMenuHijos($padres, $root){
      $hijos = [];
      foreach($padres as $padre){
        if($root['id'] == $padre['menu_padre_id']){
          $hijos = array_merge($hijos, [array_merge($padre, ['submenu' => $this->getMenuHijos($padres, $padre) ])]);
        }
      }
      return $hijos;
    }
}
