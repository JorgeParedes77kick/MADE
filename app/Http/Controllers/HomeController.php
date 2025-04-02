<?php
namespace App\Http\Controllers;

use App\Helpers\RolHelper;
use App\Models\Curriculum;
use App\Models\GrupoPequeno;
use App\Models\Temporada;
use App\Models\Usuario;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

class HomeController extends Controller {
    protected $persona;
    protected $rol;
    public function __construct() {
        $this->rol = session()->get('rol_id');
    }

    public function index() {

        $rol = session()->get('rol_id');
        if ($rol === RolHelper::$ADMIN) {
            return redirect()->route('dashboard');
        }
        if ($rol === RolHelper::$COORDINADOR) {
            return $this->coordinadorHome();
        }
        if ($rol === RolHelper::$LIDER) {
            return redirect()->route('mis-salones.index');
        }
        if ($rol === RolHelper::$MONITOR) {
            return $this->monitorHome();
        }
        // Obtener las temporadas activas y sus IDs
        $temporadas   = Temporada::activo()->get();
        $temporadasId = $temporadas->pluck('id');

        $temporadasIns = Temporada::activo()->enInscripcion()->get();

        $persona = Usuario::auth()->persona;
        // Obtener los curriculums con los ciclos y contar los alumnos en grupos pequeños

        $curriculums = collect();
        if ($temporadasIns->count() > 0) {
            $curriculums = Curriculum::activo()
                ->whereHas('ciclos.grupospequenos', function ($query) use ($temporadasId) {
                    $query->whereIn('temporada_id', $temporadasId);
                })
                ->with(['restricciones'])
                ->where(function ($Q) use ($persona) {
                    $Q->Validacion($persona)->orWhereDoesntHave('restricciones');
                })
                ->get();
        }

        // Renderizar la vista de Inertia
        return Inertia::render('Home/index', [
            'temporadas'  => $temporadas,
            'curriculums' => $curriculums,
        ]);
    }

    public function coordinadorHome() {
        $user = Usuario::auth();
        // Obtener las temporadas activas y sus IDs
        $temporadasId = Temporada::activo()->pluck('id');

        $curriculums = Curriculum::whereHas('usuarios', function ($q) use ($user) {$q->where('usuarios.id', $user->id);})
            ->whereHas('ciclos.grupospequenos', function ($query) use ($temporadasId) {
                $query->whereIn('temporada_id', $temporadasId);
            })
            ->with('ciclos', function ($q) use ($temporadasId) {
                $q->whereHas('grupospequenos', function ($query) use ($temporadasId) {
                    $query->whereIn('temporada_id', $temporadasId);
                });
            })
            ->with('ciclos.grupospequenos', function ($q) use ($temporadasId) {
                $q->whereIn('temporada_id', $temporadasId)->with('lideres');
            })

            ->get();
        return Inertia::render('Home/coordinadorHome', ['curriculums' => $curriculums]);
    }
    public function monitorHome() {
        $user = Usuario::auth();
        // Obtener las temporadas activas y sus IDs
        $temporadasId = Temporada::activo()->pluck('id');

        // Definir una función de consulta común para evitar la repetición
        $monitorQuery = function ($query) use ($user) {
            $query->whereHas('monitores', function ($q) use ($user) {$q->where('usuarios.id', $user->id);});
        };

        $grupospequenos = GrupoPequeno::whereIn('temporada_id', $temporadasId)->where($monitorQuery)
            ->with('lideres', 'ciclo.curriculum')
            ->get();

        return Inertia::render('Home/monitorHome', [
            // 'curriculums' => $curriculums,
            'grupospequenos' => $grupospequenos]);
    }
    public function lideresHome() {
        $user = Usuario::auth();
        // Obtener las temporadas activas y sus IDs
        $temporadasId = Temporada::activo()->pluck('id');

        // Definir una función de consulta común para evitar la repetición
        $lideresQuery = function ($query) use ($user) {
            $query->whereHas('lideres', function ($q) use ($user) {$q->where('usuarios.id', $user->id);});
        };

        $curriculums = Curriculum::whereHas('ciclos.grupospequenos', function ($query) use ($temporadasId, $lideresQuery) {
            $query->whereIn('temporada_id', $temporadasId)->where($lideresQuery);
        })
            ->with(['ciclos' => function ($q) use ($temporadasId, $lideresQuery) {
                $q->whereHas('grupospequenos', function ($query) use ($temporadasId, $lideresQuery) {
                    $query->whereIn('temporada_id', $temporadasId)->where($lideresQuery);
                });
            }, 'ciclos.grupospequenos' => function ($q) use ($temporadasId, $lideresQuery) {
                $q->whereIn('temporada_id', $temporadasId)->where($lideresQuery);
            }])

            ->get();

        return Inertia::render('Home/liderHome', ['curriculums' => $curriculums]);
    }
}
