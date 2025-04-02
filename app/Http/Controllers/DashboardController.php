<?php
namespace app\Http\Controllers;

use App\Helpers\RolHelper;
use App\Models\Genero;
use App\Models\GrupoPequeno;
use App\Models\Inscripcion;
use App\Models\Rol;
use App\Models\Temporada;
use App\Models\Usuario;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class DashboardController {
    private $temporadas = null;
    public function __construct() {
        $this->temporadas = Temporada::activo()->get();
    }
    public function index() {

        //Nuevos
        $temporada      = $this->temporadas->first();
        $usuariosNuevos = [];
        $data           = [];
        if ($temporada) {
            $id             = $temporada->id;
            $usuariosNuevos = $this->getUsuariosTemporadaNuevos($id);
            $data           = $this->getUsuariosTemporada($id);
        }
        $totalUsuarios = $this->getTotalUsuarios();
        $data          = (object) array_merge($data, $totalUsuarios, $usuariosNuevos);
        return Inertia::render('Home/dashboard', [
            'data'      => $data,
            'temporada' => $temporada,
        ]);
    }
    private function getTotalUsuarios() {
        $totalUsuarios = Usuario::count();

        // Para Genero
        $cantidadGenero = Genero::withCount('personas')
            ->get()
            ->map(function ($genero) {
                return ['nombre' => $genero->nombre, 'total' => $genero->personas_count];
            });
        // Para Rol
        $cantidadRoles = Rol::withCount('usuarios')
            ->get()
            ->map(function ($rol) {
                return ['nombre' => $rol->nombre, 'total' => $rol->usuarios_count];
            });
        return [
            "totalUsuarios" =>
            (object) [
                'total'   => $totalUsuarios,
                'generos' => $cantidadGenero,
                'roles'   => $cantidadRoles,
            ],
        ];
    }
    private function getUsuariosTemporadaNuevos($temporada_id) {
        $temporada         = Temporada::find($temporada_id);
        $temporadaAnterior = Temporada::find($temporada_id - 1);

        $totalUsuarios = Usuario::whereBetween('created_at', [$temporada->fecha_inicio, $temporada->fecha_cierre])->count();
        $anterior      = Usuario::whereBetween('created_at', [$temporadaAnterior->fecha_inicio, $temporadaAnterior->fecha_cierre])->count();

        $percentage      = $this->getPercentage($totalUsuarios, $anterior);
        $usuariosGeneros = Genero::withCount(['personas' => function ($q) use ($temporada) {
            $q->whereHas('user', function ($q) use ($temporada) {
                $q->whereBetween('personas.created_at', [$temporada->fecha_inicio, $temporada->fecha_cierre]);
            });
        }])->get()
            ->map(function ($genero) {
                return ['nombre' => $genero->nombre, 'total' => $genero->personas_count];
            });

        $usuariosRoles = Rol::withCount(['usuarios' => function ($q) use ($temporada) {
            $q->whereBetween('usuarios.created_at', [$temporada->fecha_inicio, $temporada->fecha_cierre]);
        }])->get()
            ->map(function ($rol) {
                return ['nombre' => $rol->nombre, 'total' => $rol->usuarios_count];
            });

        return [
            "usuariosNuevos" =>
            (object) [
                'total'      => $totalUsuarios,
                'percentage' => $percentage,
                'generos'    => $usuariosGeneros,
                'roles'      => $usuariosRoles,
            ],
        ];
    }

    private function getUsuariosTemporada($temporada_id) {
        $totalUsuarios = Usuario::wherehas('inscripciones', function ($q) {
            $q->where('inscripciones.rol_id', RolHelper::$ALUMNO);
        })
            ->wherehas('inscripciones.grupoPequeno', function ($q) use ($temporada_id) {
                $q->where('temporada_id', $temporada_id);
            })
            ->count();
        $anterior = Usuario::wherehas('inscripciones', function ($q) {
            $q->where('inscripciones.rol_id', RolHelper::$ALUMNO);
        })
            ->wherehas('inscripciones.grupoPequeno', function ($q) use ($temporada_id) {
                $q->where('temporada_id', $temporada_id - 1);
            })
            ->count();
        $percentage      = $this->getPercentage($totalUsuarios, $anterior);
        $usuariosGeneros = Genero::withCount(['personas' => function ($q) use ($temporada_id) {
            $q->wherehas('user.inscripciones', function ($q) {
                $q->where('inscripciones.rol_id', RolHelper::$ALUMNO);
            });
            $q->wherehas('user.inscripciones.grupoPequeno', function ($q) use ($temporada_id) {
                $q->where('temporada_id', $temporada_id);
            });
        }])->get()
            ->map(function ($genero) {
                return ['nombre' => $genero->nombre, 'total' => $genero->personas_count];
            });

        $usuariosRoles = Rol::withCount(['usuarios' => function ($q) use ($temporada_id) {
            $q->wherehas('inscripciones', function ($q) {
                $q->whereColumn('inscripciones.rol_id', 'roles.id');
            });
            $q->wherehas('inscripciones.grupoPequeno', function ($q) use ($temporada_id) {
                $q->where('temporada_id', $temporada_id);
            });
        }])->having('usuarios_count', '>', 0)->get()
            ->map(function ($rol) {
                return ['nombre' => $rol->nombre, 'total' => $rol->usuarios_count];
            });

        $alumnosRegionPais      = $this->getUsuariosByRegionPais($temporada_id);
        $alumnosCicloCurriculum = $this->getAlumnosByCicloCurriculum($temporada_id);
        $team                   = $this->getCursos($temporada_id);
        $inscripciones          = $this->getInscripciones($temporada_id);
        return array_merge([
            "usuariosInscritos" =>
            (object) [
                'total'      => $totalUsuarios,
                'percentage' => $percentage,
                'generos'    => $usuariosGeneros,
                'roles'      => $usuariosRoles,
            ],
        ], $alumnosRegionPais, $alumnosCicloCurriculum, $team, $inscripciones);
    }
    private function getUsuariosByRegionPais($temporada_id) {
        // Subconsulta para el total de usuarios
        $totalUsuariosSubquery = DB::raw('(select count(distinct i2.usuario_id)
                                  from inscripciones i2
                                  join grupo_pequenos gp2 on i2.grupo_pequeno_id = gp2.id
                                  where i2.rol_id = ' . RolHelper::$ALUMNO . ' and gp2.temporada_id = ' . $temporada_id . ') as total');
        $percentageInscripcionesSubquery = DB::raw('(count(*) * 100 / ( select count(*)
                                  from inscripciones i2
                                  join grupo_pequenos gp2 on i2.grupo_pequeno_id = gp2.id
                                  where i2.rol_id = ' . RolHelper::$ALUMNO . ' and gp2.temporada_id = ' . $temporada_id . ')) as percentage');
        $distinctUsuariosSubquery = DB::raw('count(distinct inscripciones.usuario_id) as cantidad');

        // Base de la consulta
        $baseQuery = Inscripcion::join('grupo_pequenos as gp', 'inscripciones.grupo_pequeno_id', '=', 'gp.id')
            ->join('usuarios as u', 'inscripciones.usuario_id', '=', 'u.id')
            ->join('personas as p', 'u.persona_id', '=', 'p.id')
            ->join('regiones as r', 'p.region_id', '=', 'r.id')
            ->join('paises as p2', 'r.pais_id', '=', 'p2.id')
            ->where('inscripciones.rol_id', RolHelper::$ALUMNO)
            ->where('gp.temporada_id', $temporada_id);

        // Usuarios por región y país
        $usuariosRegion = $baseQuery->clone()
            ->select('r.nombre as region_nombre', 'p2.nombre as pais_nombre',
                $distinctUsuariosSubquery, $totalUsuariosSubquery, $percentageInscripcionesSubquery)
            ->groupBy('r.nombre', 'p2.nombre')
            ->get();
        // Usuarios por país
        $usuariosPais = $baseQuery->clone()
            ->select('p2.nombre as pais_nombre',
                $distinctUsuariosSubquery, $totalUsuariosSubquery, $percentageInscripcionesSubquery)
            ->groupBy('p2.nombre')
            ->get();
        return [
            'inscripcionesPais'   => $usuariosPais,
            'inscripcionesRegion' => $usuariosRegion,
        ];
    }
    private function getAlumnosByCicloCurriculum($temporada_id) {
        // Subconsulta para el total de usuarios
        $totalInscripcionesSubquery = DB::raw('(select count(*)
                                  from inscripciones i2
                                  join grupo_pequenos gp2 on i2.grupo_pequeno_id = gp2.id
                                  where i2.rol_id = ' . RolHelper::$ALUMNO . ' and gp2.temporada_id = ' . $temporada_id . ') as total');
        $percentageInscripcionesSubquery = DB::raw('(count(*) * 100 / ( select count(*)
                                      from inscripciones i2
                                      join grupo_pequenos gp2 on i2.grupo_pequeno_id = gp2.id
                                      where i2.rol_id = ' . RolHelper::$ALUMNO . ' and gp2.temporada_id = ' . $temporada_id . ')) as percentage');
        $inscripcionSubquery = DB::raw('count(*) as cantidad');

        // Base de la consulta
        $baseQuery = Inscripcion::join('grupo_pequenos as gp', 'inscripciones.grupo_pequeno_id', '=', 'gp.id')
            ->join('ciclos as ci', 'gp.ciclo_id', '=', 'ci.id')
            ->join('curriculums as cur', 'ci.curriculum_id', '=', 'cur.id')
            ->where('inscripciones.rol_id', RolHelper::$ALUMNO)
            ->where('gp.temporada_id', $temporada_id);

        // Usuarios por región y país
        $inscripcionesCiclo = $baseQuery->clone()
            ->select('ci.nombre as ciclo_nombre', 'cur.nombre as curriculum_nombre',
                $inscripcionSubquery, $totalInscripcionesSubquery, $percentageInscripcionesSubquery)
            ->groupBy('ci.nombre', 'cur.nombre')
            ->get();
        // Usuarios por país
        $inscripcionesCurriculum = $baseQuery->clone()
            ->select('cur.nombre as curriculum_nombre',
                $inscripcionSubquery, $totalInscripcionesSubquery, $percentageInscripcionesSubquery)
            ->groupBy('cur.nombre')
            ->get();
        return [
            'inscripcionesCurriculum' => $inscripcionesCurriculum,
            'inscripcionesCiclo'      => $inscripcionesCiclo,
        ];
    }
    private function getCursos($temporada_id) {
        $actual     = GrupoPequeno::where('temporada_id', $temporada_id)->count();
        $anterior   = GrupoPequeno::where('temporada_id', $temporada_id - 1)->count();
        $percentage = $this->getPercentage($actual, $anterior);
        return [
            'cursos' => (object) [
                'actual'     => $actual,
                'anterior'   => $anterior,
                'percentage' => $percentage,
            ],
        ];
    }
    private function getInscripciones($temporada_id) {
        $actual = Inscripcion::whereHas('grupoPequeno', function ($q) use ($temporada_id) {
            $q->where('temporada_id', $temporada_id);
        })->count();
        $anterior = Inscripcion::whereHas('grupoPequeno', function ($q) use ($temporada_id) {
            $q->where('temporada_id', $temporada_id - 1);
        })->count();
        $percentage = $this->getPercentage($actual, $anterior);

        return [
            'inscripciones' => (object) [
                'actual'     => $actual,
                'anterior'   => $anterior,
                'percentage' => $percentage,
            ],
        ];
    }

    function getPercentage(float $actual, float $anterior) {
        $diff       = $actual - $anterior;
        $percentage = '';
        if ($anterior != 0) {
            $percentage = $diff * 100 / $anterior;
        }
        return $percentage;
    }
}
