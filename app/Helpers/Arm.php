<?php
use App\Models\Permiso;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Session;

if(!function_exists('isSuperAdmin')){
  function isSuperAdmin(): bool
  {
    return Session::get('rol_id') == 1;
  }
}

if(!function_exists('canUser')){
  function canUser($permission, $redirect = true): bool
  {
    if(Session::get('rol_id') != 1) {
      $role_id = Session::get('rol_id');
      $permissions = Cache::tags('Permission')->rememberForever("Permission.rolId.$role_id", function () use ($role_id) {
        return Permiso::whereHas('roles', function (Builder $query) use ($role_id) {
          $query->where('rol_id', $role_id);
        })->get()->pluck('llave_slug')->toArray();
      });

      if (!in_array($permission, $permissions)) {
        if ($redirect) {
          abort(403, 'No tienes permisos para acceder a este Modulo');
        } else {
          return false;
        }
      }
    }
    return true;
  }
}

?>
