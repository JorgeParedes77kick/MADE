<?php

namespace App\Http\Controllers;

use App\Helpers\Debug;
use App\Helpers\RolHelper;
use App\Models\Donacion;
use App\Http\Requests\StoreDonacionRequest;
use App\Http\Requests\UpdateDonacionRequest;
use App\Models\GrupoPequeno;
use App\Models\Inscripcion;
use App\Models\Temporada;
use App\Models\Usuario;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Inertia\Inertia;

class DonacionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Inertia\Response
     */
    public function index()
    {
        $id = Usuario::auth()->id;
        $donaciones = Donacion::where('usuario_id', $id)->get();
        return Inertia::render('Donaciones/index', [
            'donaciones' => $donaciones,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Inertia\Response
     */
    public function create()
    {
        $id = Usuario::auth()->id;
        $temporada = Temporada::where('activo', true)->select(['id', 'prefijo'])->get();
        $inscripciones = Inscripcion::where('rol_id', session()->get('rol_id'))
            ->where('usuario_id', $id)
            ->with(
                ['grupoPequeno.ciclo:id,nombre,curriculum_id',
                    'grupoPequeno.ciclo.curriculum:id,nombre',
                    'grupoPequeno.temporada:id,prefijo,activo',
                    'estadoInscripcion:id,estado',
                    'grupoPequeno.lideres',
                ])
            ->get()->toArray();

        $data = [];
        $i = 0;
        foreach($inscripciones as $ins){
            Debug::info($ins);
            $gp = $ins['grupo_pequeno'];
            if($gp["temporada_id"] == $temporada->first()->id){
                $ciclo = $gp['ciclo'];
                Debug::info($ciclo);
                $cur = $ciclo['curriculum'];
                Debug::info($cur);
                $data[$i] = [
                    "id" => $gp['id'],
                    "nombre" => $cur['nombre']
                ];
                $i++;
            }
        }

        return Inertia::render('Donaciones/form', [
            'action' => 'create',
            'inscripciones' => $data,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreDonacionRequest $request
     * @return JsonResponse
     */
    public function store(StoreDonacionRequest $request)
    {
        $input = $request->all();
        $image = $request->file('comprobanteFile');
        //Debug::info($image);
        $input['comprobante'] = $this->getBae64($image);
        //Debug::info($input['comprobante']);
        $input['usuario_id'] = Usuario::auth()->id;
        $input['rol_id'] = session()->get('rol_id');
        $temporada = Temporada::where('activo', true)->select(['id', 'prefijo'])->get();

        if($temporada){
            $input['temporada_id'] = $temporada->first()->id;
        }else{
            $input['temporada_id'] = 0;
        }

        try {

            $donation = Donacion::create($input);
            if ($donation) {
                return response()->json(["message" => "Tu donación fue registrada correctamente!"], 200);
            } else {
                return response()->json(["message" => "", 'server' => '¡No se pudo registrar tú donación, intente más tarde!'], 400);
            }
        } catch (\Throwable $th) {
            return response()->json(["message" => $th->getMessage(), 'server' => '¡Tú donación no pudo ser registrada, intente más tarde!'], 500);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Donacion  $donacion
     * @return \Illuminate\Http\Response
     */
    public function show(Donacion $donacion)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Donacion  $donacion
     * @return \Illuminate\Http\Response
     */
    public function edit(Donacion $donacion)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateDonacionRequest  $request
     * @param  \App\Models\Donacion  $donacion
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateDonacionRequest $request, Donacion $donacion)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Donacion  $donacion
     * @return \Illuminate\Http\Response
     */
    public function destroy(Donacion $donacion)
    {
        //
    }

    function updateImage(UploadedFile $imagen, $nameOldImage, $nombreDirty) {
        $nameNewImage = $nameOldImage;
        if (!is_null($imagen) && $imagen->getSize() > 0) {
            // Crear un nuevo nombre de archivo
            $nameFile = preg_replace("/[^a-zA-Z0-9]/", "", $nombreDirty);
            $nameFile = strtolower($nameFile);
            $ext = $imagen->getClientOriginalExtension();
            $nameNewImage = $nameFile . '.' . $ext; //NOMBRE QUE SE RETORNA
        }
        return $nameNewImage;
    }

    function getBae64(UploadedFile $image)
    {
        $data = file_get_contents($image->getRealPath());
        return 'data:image/' . $image->getClientOriginalExtension() . ';base64,' . base64_encode($data);
    }
}
