<?php
namespace App\Http\Controllers\Alumno;

use App\Helpers\RolHelper;
use App\Http\Controllers\Controller;
use App\Models\Inscripcion;
use App\Models\Recurso;
use App\Models\Temporada;
use App\Models\Usuario;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class RecursosController extends Controller {

    public function index() {
        $usuario       = Usuario::auth();
        $temporadasId  = Temporada::activo()->pluck('id')->toArray();
        $inscripciones = Inscripcion::where('usuario_id', $usuario->id)->where('rol_id', RolHelper::$ALUMNO)
            ->whereHas('grupoPequeno', function ($query) use ($temporadasId) {
                $query->whereIn('temporada_id', $temporadasId);
            })
            ->with(
                'grupoPequeno:id,ciclo_id,temporada_id',
                'grupoPequeno.ciclo:id,nombre,curriculum_id',
                'grupoPequeno.ciclo.curriculum:id,nombre',
                'grupoPequeno.temporada:id,prefijo',
            )
            ->whereHas('grupoPequeno.ciclo.recursos')
            ->get();

        return Inertia::render('Alumno/misRecursos', [
            'inscripciones' => $inscripciones,
        ]);
    }
    public function show(int $inscripcion_id) {
        $usuario = Usuario::auth();

        $temporadasId = Temporada::activo()->pluck('id')->toArray();
        $inscripcion  = Inscripcion::where('usuario_id', $usuario->id)
            ->where('rol_id', RolHelper::$ALUMNO)
            ->where('id', $inscripcion_id)
            ->get();

        $recursos = Recurso::
            whereHas('ciclo.gruposPequenos', function ($query) use ($temporadasId, $usuario) {
            $query->whereIn('temporada_id', $temporadasId)
                ->whereHas('alumnos', function ($query) use ($usuario) {
                    $query->where('usuarios.id', $usuario->id);
                });
        })->get();
        $ciclo = $recursos->first()->ciclo()->with('curriculum:id,nombre')->first();

        return Inertia::render('Alumno/recursosShow', [
            'recursos' => $recursos,
            'ciclo'    => $ciclo,
        ]);
    }

}
