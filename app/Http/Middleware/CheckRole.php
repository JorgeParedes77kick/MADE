<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next) {
        if (
            Auth::check() &&
            $request->isMethod('get') &&
            !$request->wantsJson() &&
            $request->path() != 'home'
        ) {
            $rol_id = $request->session()->get('rol_id');
            $check_path_rol_selected = DB::table('roles as r')
                ->join('roles_menus as rm', 'rm.rol_id', '=', 'r.id')
                ->join('menus as m', 'm.id', '=', 'rm.menu_id')
                ->where('r.id', $rol_id)
                ->where('m.url_ref', 'like', '%' . $request->path() . '%')
                ->count();
            // ***COMENTADO TEMPORALMENTE REVISAR PARA PAGINAS QUE NO SON INDEX O PRINCIPALES, PORQUE BLOQUEA LAS PAGINAS DE SHOW,EDIT,CREATE
            // if ($check_path_rol_selected == 0) {
            //     return redirect('home');
            // }
        }

        return $next($request);
    }
}
