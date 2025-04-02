<?php

namespace App\Http\Controllers;

use App\Models\GlobalProject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Throwable;

class GlobalController extends Controller {
    /**
     * Display a listing of the resource.
     *
     */
    public function index() {
        $globales = GlobalProject::all();
        $tipos = ['number', 'boolean', 'string', 'date'];
        return Inertia::render('Globales/index', ['globales' => $globales, 'tipos_cast' => $tipos]);
    }

    /**
     * Show the form for creating a new resource.
     *
     */
    public function create() {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     */
    public function store(Request $request) {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     */
    public function show($id) {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     */
    public function edit($id) {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     */
    public function update(Request $request, $id) {
        try {
            DB::beginTransaction();
            $global = GlobalProject::find($id);
            $state = $global->update($request->all());
            DB::commit();
            if ($state) {
                return response()->json(["message" => 'La Global ' . $global->nombre . ' fue actualizada exitosamente!', "global" => $global], 200);
            } else {
                return response()->json(["message" => "", 'server' => '¡La Global ' . $global->nombre . ' no pudo ser actualizada, intente más tarde!'], 400);
            }
        } catch (Throwable $th) {
            DB::rollBack();
            return response()->json(["message" => $th->getMessage(), 'server' => '¡La Global ' . $global->nombre . ' no pudo ser actualizada, intente más tarde!'], 500);

        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     */
    public function destroy($id) {
        //
    }
}
