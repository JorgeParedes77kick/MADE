<?php

namespace App\Http\Controllers;

use App\Http\Requests\RestriccionRequest;
use App\Models\Curriculum;
use App\Models\Restriccion;
use App\Models\TipoRestriccion;
use Illuminate\Http\Request;
use Inertia\Inertia;

class RestriccionController extends Controller {
    /**
     * Display a listing of the resource.
     *
     */
    public function index() {
        $restricciones = Restriccion::all();
        $restricciones = $restricciones->sortBy(function ($restriccion) {
            return $restriccion->curriculum->id;
        })->values();
        return Inertia::render('Restricciones/index', [
            'restricciones' => $restricciones,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     */
    public function create() {
        $tipos = TipoRestriccion::orderBy('nombre')->get();
        $curriculumns = Curriculum::orderBy('nombre')->get();
        return Inertia::render('Restricciones/form', [
            'action' => 'create',
            'tipos' => $tipos,
            'curriculums' => $curriculumns,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     */
    public function store(RestriccionRequest $request) {
        $input = $request->only('nombre', 'tipo_restriccion_id', 'valor_restriccion', 'curriculum_id');
        $restriccion = Restriccion::create($input);

        if ($restriccion) {
            return response()->json(["message" => "La Restricción fue creada exitosamente!"], 200);
        } else {
            return response()->json(["message" => "", 'server' => '¡La Restricción no pudo ser creada, intente más tarde!'], 400);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     */
    public function show($id) {
        $restriccion = Restriccion::find($id);
        $tipos = TipoRestriccion::orderBy('nombre')->get();
        $curriculumns = Curriculum::orderBy('nombre')->get();
        return Inertia::render('Restricciones/form', [
            'action' => 'show',
            'restriccion' => $restriccion,
            'tipos' => $tipos,
            'curriculums' => $curriculumns,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     */
    public function edit($id) {
        $restriccion = Restriccion::find($id);
        $tipos = TipoRestriccion::orderBy('nombre')->get();
        $curriculumns = Curriculum::orderBy('nombre')->get();
        return Inertia::render('Restricciones/form', [
            'action' => 'edit',
            'restriccion' => $restriccion,
            'tipos' => $tipos,
            'curriculums' => $curriculumns,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     */
    public function update(RestriccionRequest $request, $id) {
        $input = $request->only('nombre', 'tipo_restriccion_id', 'valor_restriccion', 'curriculum_id');
        $restriccion = Restriccion::whereId($id);

        try {
            $state = $restriccion->update($input);

            if ($state) {
                return response()->json(["message" => "La Restricción fue actualizada exitosamente!"], 200);
            } else {
                return response()->json(["message" => "", 'server' => '¡La Restricción no pudo ser actualizada, intente más tarde!'], 400);
            }
        } catch (\Throwable $th) {
            return response()->json(["message" => $th->getMessage(), 'server' => '¡La Restricción no pudo ser actualizada, intente más tarde!'], 500);
        }

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     */
    public function destroy($id) {
        $restriccion = Restriccion::whereId($id);
        try {
            $state = $restriccion->delete();
            if ($state) {
                return response()->json(["message" => "La Restricción fue eliminada exitosamente!"], 200);
            } else {
                return response()->json(["message" => "", 'server' => '¡La Restricción no pudo ser eliminada, intente más tarde!'], 400);

            }
        } catch (\Throwable $th) {
            return response()->json(["message" => $th->getMessage(), 'server' => '¡La Restricción no pudo ser eliminada, intente más tarde!'], 500);
        }

    }

}
