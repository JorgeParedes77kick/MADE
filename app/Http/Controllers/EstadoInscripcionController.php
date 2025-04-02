<?php

namespace App\Http\Controllers;

use App\Http\Requests\EstadoRequest;
use App\Models\EstadoInscripcion;
use Illuminate\Http\Request;
use Inertia\Inertia;

class EstadoInscripcionController extends Controller {
    /**
     * Display a listing of the resource.
     *
     */
    public function index() {
        $estados = EstadoInscripcion::all();
        return Inertia::render('EstadosInscripcion/index', [
            'estados' => $estados,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     */
    public function create() {
        return Inertia::render('EstadosInscripcion/form', [
            'action' => 'create',
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     */
    public function store(EstadoRequest $request) {
        $input = $request->all();
        $estado = EstadoInscripcion::create($input);

        if ($estado) {
            return response()->json(["message" => "El Estado fue creada exitosamente!"], 200);
        } else {
            return response()->json(["message" => "", 'server' => '¡El Estado no pudo ser creada, intente más tarde!'], 400);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     */
    public function show($id) {
        $estado = EstadoInscripcion::find($id);
        return Inertia::render('EstadosInscripcion/form', [
            'action' => 'show',
            'estInscripcion' => $estado,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     */
    public function edit($id) {
        $estado = EstadoInscripcion::find($id);
        return Inertia::render('EstadosInscripcion/form', [
            'action' => 'edit',
            'estInscripcion' => $estado,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     */
    public function update(EstadoRequest $request, $id) {
        $input = $request->all();
        $estado = EstadoInscripcion::find($id);

        try {
            $state = $estado->update($input);

            if ($state) {
                return response()->json(["message" => "El Estado fue actualizada exitosamente!"], 200);
            } else {
                return response()->json(["message" => "", 'server' => '¡El Estado no pudo ser actualizada, intente más tarde!'], 400);
            }
        } catch (\Throwable $th) {
            return response()->json(["message" => $th->getMessage(), 'server' => '¡El Estado no pudo ser actualizada, intente más tarde!'], 500);
        }

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     */
    public function destroy($id) {
        $estado = EstadoInscripcion::find($id);
        try {
            $state = $estado->delete();
            if ($state) {
                return response()->json(["message" => "El Estado fue eliminada exitosamente!"], 200);
            } else {
                return response()->json(["message" => "", 'server' => '¡El Estado no pudo ser eliminada, intente más tarde!'], 400);

            }
        } catch (\Throwable $th) {
            return response()->json(["message" => $th->getMessage(), 'server' => '¡El Estado no pudo ser eliminada, intente más tarde!'], 500);
        }

    }
}
