<?php

namespace App\Http\Controllers;

use App\Models\Region;
use Illuminate\Http\Request;

class RegionController extends Controller
{
  public function list(int $pais) {
    $regiones = Region::select('id', 'nombre')
      ->where('pais_id', '=', $pais)
      ->orderBy('nombre', 'asc')->get();
    return response($regiones, 200);
  }
}
