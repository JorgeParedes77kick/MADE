<?php

namespace App\Http\Controllers;

use App\Models\Pais;
use Illuminate\Http\Request;

class PaisController extends Controller
{
  public function list() {
    $paises = Pais::select('id', 'nombre')->orderBy('nombre', 'asc')->get();
    return response($paises, 200);
  }
}
