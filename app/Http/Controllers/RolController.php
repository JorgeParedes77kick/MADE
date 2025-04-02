<?php

namespace App\Http\Controllers;

use App\Http\Requests\RolRequest;
use App\Models\Rol;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;

class RolController extends Controller {
    /**
     * Display a listing of the resource.
     *
     */
    public function index() {
        $roles = Rol::all();
        return Inertia::render('Roles/index', [
            'roles' => $roles,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     */
    public function create() {
        return Inertia::render('Roles/form', [
            'action' => 'create',
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     */
    public function store(RolRequest $request) {
        $input = $request->all();
        $rol = Rol::create($input);

        if ($rol) {
            return response()->json(["message" => "El Rol fue creada exitosamente!"], 200);
        } else {
            return response()->json(["message" => "", 'server' => '¡El Rol no pudo ser creada, intente más tarde!'], 400);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     */
    public function show($id) {
        $rol = Rol::find($id);
        return Inertia::render('Roles/form', [
            'action' => 'show',
            'rol' => $rol,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     */
    public function edit($id) {
        $rol = Rol::find($id);
        return Inertia::render('Roles/form', [
            'action' => 'edit',
            'rol' => $rol,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     */
    public function update(RolRequest $request, $id) {
        $input = $request->all();
        $rol = Rol::find($id);

        try {
            $state = $rol->update($input);

            if ($state) {
                return response()->json(["message" => "El Rol fue actualizada exitosamente!"], 200);
            } else {
                return response()->json(["message" => "", 'server' => '¡El Rol no pudo ser actualizada, intente más tarde!'], 400);
            }
        } catch (\Throwable $th) {
            return response()->json(["message" => $th->getMessage(), 'server' => '¡El Rol no pudo ser actualizada, intente más tarde!'], 500);
        }

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     */
    public function destroy($id) {
        $rol = Rol::find($id);
        try {
            $state = $rol->delete();
            if ($state) {
                return response()->json(["message" => "El Rol fue eliminada exitosamente!"], 200);
            } else {
                return response()->json(["message" => "", 'server' => '¡El Rol no pudo ser eliminada, intente más tarde!'], 400);

            }
        } catch (\Throwable $th) {
            return response()->json(["message" => $th->getMessage(), 'server' => '¡El Rol no pudo ser eliminada, intente más tarde!'], 500);
        }

    }

    /**
     * Apply a new rol user in session.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function applyRol(Request $request) {
        $role = Rol::whereId($request->input('role_id'))->first();
        if ($role) {
            $request->session()->put('rol_id', $role->id);
            return response()->json(["message" => "Cambio de Rol Exitoso!"], 200);
        }
        return response()->json(["message" => "No es posible cambiar el rol!"], 403);
    }

    /**
     * get rol user in session.
     *
     * @return JsonResponse
     */
    public function getRolSession() {
        return response()->json(["rol" => session('rol_id')], 200);
    }

}
