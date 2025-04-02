<?php

namespace App\Http\Controllers;

use App\Models\Genero;

class GeneroController extends Controller
{
  public function list() {
    $generos = Genero::select('id', 'nombre')->orderBy('nombre', 'asc')->get();
    return response($generos, 200);
  }
}
