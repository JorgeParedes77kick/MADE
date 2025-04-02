<?php

namespace App\Http\Controllers;

use App\Http\Requests\RecursoRequest;
use App\Models\Curriculum;
use App\Models\Recurso;
use Illuminate\Http\Request;
use Inertia\Inertia;

class RecursoController extends Controller {
    /**
     * Display a listing of the resource.
     *
     */
    public function index() {
        $recursos = Recurso::with('ciclo.curriculum')
            ->whereHas('ciclo.curriculum', function ($query) {$query->activo();})->get();
        return Inertia::render('Recursos/index', [
            'recursos' => $recursos,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     */
    public function create() {
        $curriculums = Curriculum::with('ciclos')->activo()->get();
        return Inertia::render('Recursos/form', [
            'action' => 'create',
            'curriculums' => $curriculums,

        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     */
    public function store(RecursoRequest $request) {
        $inputs = $request->all();
        $recurso = Recurso::create($inputs);

        try {

            if ($recurso) {
                return response()->json(["message" => "El Recurso fue creado exitosamente!"], 200);
            } else {
                return response()->json(["message" => "", 'server' => '¡El Recurso no pudo ser creado, intente más tarde!'], 400);
            }
        } catch (\Throwable $th) {
            return response()->json(["message" => $th->getMessage(), 'server' => '¡El Recurso no pudo ser creado, intente más tarde!'], 500);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     */
    public function show($id) {
        $recurso = Recurso::whereId($id)->with('ciclo.curriculum')->first();
        $curriculums = Curriculum::with('ciclos')->activo()->get();
        return Inertia::render('Recursos/form', [
            'action' => 'show',
            'recurso' => $recurso,
            'curriculums' => $curriculums,

        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     */
    public function edit($id) {
        $recurso = Recurso::whereId($id)->with('ciclo.curriculum')->first();
        $curriculums = Curriculum::with('ciclos')->activo()->get();
        return Inertia::render('Recursos/form', [
            'action' => 'edit',
            'recurso' => $recurso,
            'curriculums' => $curriculums,

        ]);
    }
    /**
     * @param  \Illuminate\Http\Request  $request
     */
    public function update(RecursoRequest $request, $id) {
        $input = $request->all();
        $recurso = Recurso::find($id);

        try {
            $state = $recurso->update($input);
            if ($state) {
                return response()->json(["message" => "El Recurso fue actualizado exitosamente!"], 200);
            } else {
                return response()->json(["message" => "", 'server' => '¡El Recurso no pudo ser actualizado, intente más tarde!'], 400);
            }
        } catch (\Throwable $th) {
            return response()->json(["message" => $th->getMessage(), 'server' => '¡El Recurso no pudo ser actualizado, intente más tarde!'], 500);
        }
    }
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     */
    public function destroy($id) {
        $restriccion = Recurso::find($id);
        try {
            $state = $restriccion->delete();
            if ($state) {
                return response()->json(["message" => "El Recurso fue eliminada exitosamente!"], 200);
            } else {
                return response()->json(["message" => "", 'server' => '¡El Recurso no pudo ser eliminada, intente más tarde!'], 400);

            }
        } catch (\Throwable $th) {
            return response()->json(["message" => $th->getMessage(), 'server' => '¡El Recurso no pudo ser eliminada, intente más tarde!'], 500);
        }

    }
}
