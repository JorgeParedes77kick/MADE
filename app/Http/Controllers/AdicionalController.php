<?php

namespace App\Http\Controllers;

use App\Models\Adicional;
use App\Models\Curriculum;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class AdicionalController extends Controller {

    private $types = ['string', 'boolean', 'number'];
    /**
     * Display a listing of the resource.
     *
     */
    public function index() {

        $curriculums = Curriculum::with('adicionales')
            ->activo()->select('id', 'nombre')->orderBy('nombre')->get();
        $curriculums = $curriculums->sortBy(function ($curriculum) {
            return -1 * $curriculum->adicionales->count();
        })->values();

        return Inertia::render('AdicionalesCurriculum/index', [
            'curriculums' => $curriculums,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     */
    public function create() {
        $curriculums = Curriculum::with('adicionales')->activo()->select('id', 'nombre')->orderBy('nombre')->get();
        return Inertia::render('AdicionalesCurriculum/form', [
            'action' => 'create',
            'curriculums' => $curriculums,
            'types' => $this->types,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     */
    public function store(Request $request) {
        $curriculum_id = $request->curriculum_id;
        $adicionales = $request->adicionales;

        DB::beginTransaction();
        try {
            foreach ($adicionales as $adi) {
                $adiObj = (object) $adi;
                if (!empty($adiObj->delete)) {
                    $item = Adicional::whereId($adiObj->id)->doesntHave('adicionalesInscripciones')->first();
                    if (is_null($item)) {
                        throw new Exception('Adicional con Inscripciones');
                    }
                    $item->delete();
                } else if (!empty($adiObj->id)) {
                    Adicional::find($adiObj->id)->update($adi);
                } else {
                    $adi['curriculum_id'] = $curriculum_id;
                    Adicional::create($adi);
                } # code...
            }
            DB::commit();
            return response()->json(["message" => "Los Adicionales fueron creados y/o actualizados correctamente!"], 200);

        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(["message" => "Error al crear o actualizar adicionales"], 500);

        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     */
    public function show($id) {
        $curriculum = Curriculum::whereId($id)->with('adicionales')
            ->activo()->select('id', 'nombre')->orderBy('nombre')->first();
        return Inertia::render('AdicionalesCurriculum/form', [
            'action' => 'show',
            'curriculum' => $curriculum,
            'types' => $this->types,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     */
    public function edit($id) {
        $curriculum = Curriculum::whereId($id)->with('adicionales')
            ->activo()->select('id', 'nombre')->orderBy('nombre')->first();
        return Inertia::render('AdicionalesCurriculum/form', [
            'action' => 'edit',
            'curriculum' => $curriculum,
            'types' => $this->types,
        ]);
    }

}
