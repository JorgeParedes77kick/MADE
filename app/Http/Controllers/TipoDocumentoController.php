<?php

namespace App\Http\Controllers;

use App\Models\TipoDocumento;
use App\Http\Requests\StoreTipoDocumentoRequest;
use App\Http\Requests\UpdateTipoDocumentoRequest;

class TipoDocumentoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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
     * @param  \App\Http\Requests\StoreTipoDocumentoRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreTipoDocumentoRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\TipoDocumento  $tipoDocumento
     * @return \Illuminate\Http\Response
     */
    public function show(TipoDocumento $tipoDocumento)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\TipoDocumento  $tipoDocumento
     * @return \Illuminate\Http\Response
     */
    public function edit(TipoDocumento $tipoDocumento)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateTipoDocumentoRequest  $request
     * @param  \App\Models\TipoDocumento  $tipoDocumento
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateTipoDocumentoRequest $request, TipoDocumento $tipoDocumento)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\TipoDocumento  $tipoDocumento
     * @return \Illuminate\Http\Response
     */
    public function destroy(TipoDocumento $tipoDocumento)
    {
        //
    }
}
