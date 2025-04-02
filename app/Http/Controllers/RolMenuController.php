<?php

namespace App\Http\Controllers;

use App\Helpers\Debug;
use App\Models\Menu;
use App\Models\Rol;
use App\Models\RolMenu;
use App\Http\Requests\StoreRolMenuRequest;
use App\Http\Requests\UpdateRolMenuRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class RolMenuController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Inertia\Response
     */
    public function index()
    {
      $roles = Rol::orderBy('id')->get();
      $menus = Menu::getMenu(false);
      $rolesMenus = RolMenu::orderBy('rol_id')->get();

      return Inertia::render('RolMenu/index', [
        'roles' => $roles,
        'menus' => $menus,
        'rolesMenus' => $rolesMenus,
      ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreRolMenuRequest $request
     * @return JsonResponse
     */
    public function store(StoreRolMenuRequest $request)
    {
      $menu = Menu::findOrFail($request->menu_id);
      try {
        DB::beginTransaction();
        if($request->state == 1){
          $menu->roles()->attach($request->rol_id);
          DB::commit();
          return response()->json(["message" => "El Rol/Menu se vincularon exitosamente!"], 200);
        }else{
          $menu->roles()->detach($request->rol_id);
          DB::commit();
          return response()->json(["message" => "El Rol/Menu se desvincularon exitosamente!"], 200);
        }
      } catch (\Throwable $th) {
        Debug::info($th);
        DB::rollBack();
        return response()->json(["message" => $th->getMessage(), 'server' => '¡El Rol/Menu no pudo ser actualizado, intente más tarde!'], 500);
      }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\RolMenu  $rolMenu
     * @return \Illuminate\Http\Response
     */
    public function show(RolMenu $rolMenu)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\RolMenu  $rolMenu
     * @return \Illuminate\Http\Response
     */
    public function edit(RolMenu $rolMenu)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateRolMenuRequest  $request
     * @param  \App\Models\RolMenu  $rolMenu
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateRolMenuRequest $request, RolMenu $rolMenu)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\RolMenu  $rolMenu
     * @return \Illuminate\Http\Response
     */
    public function destroy(RolMenu $rolMenu)
    {
        //
    }
}
