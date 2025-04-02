<?php

namespace App\Http\Controllers;

use App\Models\Nacionalidad;
use Illuminate\Http\Request;

class NacionalidadController extends Controller
{
  public function list() {
    $nacionalidades = Nacionalidad::select('id', 'nombre')->orderBy('nombre', 'asc')->get();
    return response($nacionalidades, 200);
  }
}
