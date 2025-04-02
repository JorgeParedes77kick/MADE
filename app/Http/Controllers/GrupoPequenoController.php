<?php
namespace App\Http\Controllers;

use App\Helpers\Cast;
use App\Helpers\Debug;
use App\Helpers\RolHelper;
use App\Models\Ciclo;
use App\Models\Curriculum;
use App\Models\GrupoPequeno;
use App\Models\Temporada;
use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class GrupoPequenoController extends Controller {
    /**
     * Display a listing of the resource.
     *
     */
    public function index(Request $request) {
        $rol = session()->get('rol_id');

        $gruposPequenos = $this->find($request);

        $temporadas = cache()->remember('temporadas', 60 * 20, function () {
            return Temporada::select('id', 'prefijo')->orderBy('prefijo', 'desc')->get()
                ->makeHidden(['semanas', 'fecha_inicio_w', 'fecha_cierre_w', 'fecha_extension_w']);
        });
        $curriculums = collect();
        $ciclos      = collect();
        if ($rol === RolHelper::$COORDINADOR) {
            $idsCurriculum = Usuario::auth()->curriculums()->pluck('curriculums.id');
            if ($idsCurriculum->count() > 0) {
                $curriculums = Curriculum::activo()->whereIn('curriculums.id', $idsCurriculum)->get();
                $ciclos      = Ciclo::whereHas('curriculum', function ($q) use ($idsCurriculum) {
                    $q->activo()->whereIn('curriculums.id', $idsCurriculum);
                })->select('nombre')->distinct()->get();
            }
        } else {
            $curriculums = cache()->remember('curriculums', 60 * 20, function () {
                return Curriculum::activo()->select('nombre')->get();
            });
            $ciclos = cache()->remember('ciclos', 60 * 60, function () {
                return Ciclo::whereHas('curriculum', function ($q) {$q->activo();})->select('nombre')
                    ->distinct()->get();
            });
        }
        $dias = collect(['lunes', 'martes', 'miércoles', 'jueves', 'viernes', 'sábado', 'domingo', 'none']);

        $form         = $request->except('perPage', 'page', 'temporadas');
        $temporadasId = Cast::castParams($request->input('temporadas', []), 'int');
        $form         = array_merge($form, $request->only('buscador'), ['temporadas' => $temporadasId]);
        return Inertia::render('GruposPequenos/index', [
            'gruposPequenos' => $gruposPequenos,
            'temporadas'     => $temporadas,
            'curriculums'    => $curriculums,
            'ciclos'         => $ciclos,
            'dias'           => $dias,
            'action'         => 'historico',
            'form'           => $form,
        ]);
    }

    public function horario(Request $request) {
        $gruposPequenos = $this->find($request, true);
        $rol            = session()->get('rol_id');

        $temporadas = cache()->remember('temporadasActiva', 60 * 20, function () {
            return Temporada::select('id', 'prefijo')->orderBy('prefijo', 'desc')->activo()->get()
                ->makeHidden(['semanas', 'fecha_inicio_w', 'fecha_cierre_w', 'fecha_extension_w']);
        });
        if ($rol === RolHelper::$COORDINADOR) {
            $idsCurriculum = Usuario::auth()->curriculums()->pluck('curriculums.id');
            $curriculums   = Curriculum::activo()->whereIn('curriculums.id', $idsCurriculum)->get();
            $ciclos        = Ciclo::whereHas('curriculum', function ($q) use ($idsCurriculum) {
                $q->activo()->whereIn('curriculums.id', $idsCurriculum);
            })->select('nombre')->distinct()->get();
        } else {
            $curriculums = cache()->remember('curriculums', 60 * 20, function () {
                return Curriculum::activo()->select('nombre')->get();
            });
            $ciclos = cache()->remember('ciclos', 60 * 60, function () {
                return Ciclo::whereHas('curriculum', function ($q) {$q->activo();})->select('nombre')
                    ->distinct()->get();
            });
        }
        $dias = collect(['lunes', 'martes', 'miércoles', 'jueves', 'viernes', 'sábado', 'domingo', 'none']);

        $form         = $request->except('perPage', 'page', 'temporadas');
        $temporadasId = Cast::castParams($request->input('temporadas', []), 'int');
        $form         = array_merge($form, $request->only('buscador'), ['temporadas' => $temporadasId]);

        return Inertia::render('GruposPequenos/index', [
            'gruposPequenos' => $gruposPequenos,
            'temporadas'     => $temporadas,
            'curriculums'    => $curriculums,
            'ciclos'         => $ciclos,
            'dias'           => $dias,
            'action'         => 'horarios',
            'form'           => $form,

        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     */
    public function create() {
        $temporadas = Temporada::where('activo', true)->select(['id', 'prefijo'])->get();

        $rol         = session()->get('rol_id');
        $curriculums = collect();
        if ($rol === RolHelper::$COORDINADOR) {
            $idsCurriculum = Usuario::auth()->curriculums()->pluck('curriculums.id');
            if ($idsCurriculum->count() > 0) {
                $curriculums = Curriculum::activo()->select(['id', 'nombre'])->with('ciclos:id,nombre,curriculum_id')->whereIn('curriculums.id', $idsCurriculum)->get();
            }
        } else {
            $curriculums = Curriculum::activo()->select(['id', 'nombre'])->with('ciclos:id,nombre,curriculum_id')->get();
        }

        $dias    = collect(['lunes', 'martes', 'miércoles', 'jueves', 'viernes', 'sábado', 'domingo', 'none']);
        $lideres = Usuario::select(['usuarios.id', 'usuarios.nick_name', 'usuarios.email', 'personas.id as persona_id', 'personas.nombre', 'personas.apellido',
            DB::raw("CONCAT(personas.nombre, ' ', personas.apellido, ' - ', usuarios.email) as fullNombre"),
        ])->join('personas', 'usuarios.persona_id', '=', 'personas.id')->whereHas('roles', function ($q) {$q->where('roles.id', 3);})->get();

        $monitores = Usuario::select(['usuarios.id', 'usuarios.nick_name', 'usuarios.email', 'personas.id as persona_id', 'personas.nombre', 'personas.apellido',
            DB::raw("CONCAT(personas.nombre, ' ', personas.apellido, ' - ', usuarios.email) as fullNombre"),
        ])
            ->join('personas', 'usuarios.persona_id', '=', 'personas.id')
            ->whereHas('roles', function ($q) {$q->where('roles.id', 4);})->get();

        return Inertia::render('GruposPequenos/form', [
            'action'      => 'create',
            'temporadas'  => $temporadas,
            'curriculums' => $curriculums,
            'dias'        => $dias,
            'lideres'     => $lideres,
            'monitores'   => $monitores,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     */
    public function store(Request $request) {
        // Extraer líderes y monitores
        $lideres   = $request->input('lideres', []);
        $monitores = $request->input('monitores', []);

        // Remover líderes y monitores de los inputs
        $inputs = $request->except(['lideres', 'monitores']);

        try {
            DB::beginTransaction();
            // Crear el grupo pequeño
            $grupopequeno = GrupoPequeno::create($inputs);

            // Preparar inscripciones de líderes
            $lideresInscripciones = array_map(function ($lider) use ($grupopequeno) {
                return [
                    'usuario_id'            => $lider['id'],
                    'rol_id'                => 4, // Rol de líder
                    'grupo_pequeno_id'      => $grupopequeno->id,
                    'estado_inscripcion_id' => 5,
                    'info_adicional'        => 'Lider',
                ];
            }, $lideres);

            // Preparar inscripciones de monitores
            $monitoresInscripciones = array_map(function ($monitor) use ($grupopequeno) {
                return [
                    'usuario_id'            => $monitor['id'],
                    'rol_id'                => 3, // Rol de monitor
                    'grupo_pequeno_id'      => $grupopequeno->id,
                    'estado_inscripcion_id' => 6,
                    'info_adicional'        => 'Monitor',
                ];
            }, $monitores);

            // Combinar ambas inscripciones
            $inscripciones = array_merge($lideresInscripciones, $monitoresInscripciones);

            $states = $grupopequeno->inscripciones()->createMany($inscripciones);
            DB::commit();
            // Debug::info($states);
            if ($states) {
                return response()->json(["message" => "El horario Grupo pequeño fue creado exitosamente, con sus lideres y monitores!"], 200);
            } else {
                return response()->json(["message" => "", 'server' => '¡El horario Grupo pequeño no pudo ser creado, intente más tarde!'], 400);
            }

        } catch (\Throwable $th) {
            //throw $th;
            DB::rollBack();
            return response()->json(["message" => $th->getMessage(), 'server' => '¡El horario Grupo pequeño no pudo ser creado, intente más tarde!'], 500);

        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     */
    public function show($id) {
        $grupoPequeno = GrupoPequeno::where('id', $id)->with(
            'ciclo.curriculum', 'temporada',
            'inscripcionesAlumnos.estadoInscripcion',
            'inscripcionesAlumnos.usuario',
            'lideres', 'monitores'
        )->orderBy('temporada_id', 'desc')->first();
        // Agrupar los usuarios por su rol (líderes y monitores)

        return Inertia::render('GruposPequenos/show', [
            'action'       => 'show',
            'grupoPequeno' => $grupoPequeno,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     */
    public function edit($id) {
        $grupoPequeno = GrupoPequeno::whereId($id)
            ->with([
                'temporada:id,prefijo',
                'ciclo:id,nombre,curriculum_id',
                'ciclo.curriculum:id,nombre',
                'inscripciones' => function ($query) {
                    // Filtrar solo por roles 3 (Lideres) y 4 (Monitores)
                    $query->whereIn('rol_id', [3, 4])->with('usuario.persona:id,nombre,apellido');
                },
            ])
            ->withCount(['inscripciones as alumnos_contar' => function ($query) {
                $query->where('rol_id', 5);
            }])
            ->firstOrFail(); // Usar firstOrFail para obtener un error 404 si no se encuentra el grupo

        // Filtrar líderes y monitores desde las inscripciones
        $lideres = $grupoPequeno->inscripciones
            ->where('rol_id', 3)
            ->map(function ($inscripcion) {
                $usuario             = $inscripcion->usuario;
                $usuario->fullNombre = "{$usuario->persona->nombre} {$usuario->persona->apellido} - {$usuario->email}";
                return $usuario;
            })->values();

        $monitores = $grupoPequeno->inscripciones
            ->where('rol_id', 4)
            ->map(function ($inscripcion) {
                $usuario             = $inscripcion->usuario;
                $usuario->fullNombre = "{$usuario->persona->nombre} {$usuario->persona->apellido} - {$usuario->email}";
                return $usuario;
            })->values();
        // $alumnos_contar = $grupoPequeno->inscripciones->where('rol_id', 5)->count();
        $grupoPequeno->lideres   = $lideres;
        $grupoPequeno->monitores = $monitores;
        // $grupoPequeno->alumnos_contar = $alumnos_contar;
        // unset($grupoPequeno->inscripciones);

        $temporadas = Temporada::where('activo', true)->select(['id', 'prefijo'])->get();

        $rol         = session()->get('rol_id');
        $curriculums = collect();
        if ($rol === RolHelper::$COORDINADOR) {
            $idsCurriculum = Usuario::auth()->curriculums()->pluck('curriculums.id');
            if ($idsCurriculum->count() > 0) {
                $curriculums = Curriculum::activo()->select(['id', 'nombre'])->with('ciclos:id,nombre,curriculum_id')->whereIn('curriculums.id', $idsCurriculum)->get();
            }
        } else {
            $curriculums = Curriculum::activo()->select(['id', 'nombre'])->with('ciclos:id,nombre,curriculum_id')->get();
        }
        $dias    = collect(['lunes', 'martes', 'miércoles', 'jueves', 'viernes', 'sábado', 'domingo', 'none']);
        $lideres = Usuario::select(['usuarios.id', 'usuarios.nick_name', 'usuarios.email', 'personas.id as persona_id', 'personas.nombre', 'personas.apellido',
            DB::raw("CONCAT(personas.nombre, ' ', personas.apellido, ' - ', usuarios.email) as fullNombre"),
        ])->join('personas', 'usuarios.persona_id', '=', 'personas.id')->whereHas('roles', function ($q) {$q->where('roles.id', 3);})->get();

        $monitores = Usuario::select(['usuarios.id', 'usuarios.nick_name', 'usuarios.email', 'personas.id as persona_id', 'personas.nombre', 'personas.apellido',
            DB::raw("CONCAT(personas.nombre, ' ', personas.apellido, ' - ', usuarios.email) as fullNombre"),
        ])
            ->join('personas', 'usuarios.persona_id', '=', 'personas.id')
            ->whereHas('roles', function ($q) {$q->where('roles.id', 4);})->get();

        return Inertia::render('GruposPequenos/form', [
            'action'       => 'edit',
            'temporadas'   => $temporadas,
            'curriculums'  => $curriculums,
            'dias'         => $dias,
            'lideres'      => $lideres,
            'monitores'    => $monitores,
            'grupoPequeno' => $grupoPequeno,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     */
    public function update(Request $request, $id) {
        // Extraer líderes y monitores del request
        $lideres   = $request->input('lideres', []);
        $monitores = $request->input('monitores', []);

        // Remover líderes y monitores de los inputs para crear el grupo
        $inputs = $request->except(['lideres', 'monitores']);

        try {
            DB::beginTransaction();

            // Obtener el grupo pequeño existente
            $grupoPequeno = GrupoPequeno::findOrFail($id);

            // Actualizar la información del grupo pequeño
            $grupoPequeno->update($inputs);

            // Obtener las inscripciones actuales de líderes y monitores
            $inscripcionesActuales = $grupoPequeno->inscripciones()->whereIn('rol_id', [3, 4])->get();

            // Obtener los IDs actuales de líderes y monitores
            $lideresActuales   = $inscripcionesActuales->where('rol_id', 3)->pluck('usuario_id')->toArray();
            $monitoresActuales = $inscripcionesActuales->where('rol_id', 4)->pluck('usuario_id')->toArray();

            // Obtener los nuevos IDs de líderes y monitores desde el request
            $nuevosLideres   = collect($lideres)->pluck('id')->toArray();
            $nuevosMonitores = collect($monitores)->pluck('id')->toArray();

            // Determinar los líderes y monitores a agregar y a eliminar
            $lideresAEliminar   = array_diff($lideresActuales, $nuevosLideres);
            $monitoresAEliminar = array_diff($monitoresActuales, $nuevosMonitores);

            $lideresAAgregar   = array_diff($nuevosLideres, $lideresActuales);
            $monitoresAAgregar = array_diff($nuevosMonitores, $monitoresActuales);

            // Eliminar inscripciones de los líderes y monitores que ya no están
            $grupoPequeno->inscripciones()->whereIn('usuario_id', $lideresAEliminar)->where('rol_id', 3)->delete();
            $grupoPequeno->inscripciones()->whereIn('usuario_id', $monitoresAEliminar)->where('rol_id', 4)->delete();

            // Agregar nuevas inscripciones de líderes
            $lideresInscripciones = array_map(function ($lider) use ($grupoPequeno) {
                return [
                    'usuario_id'            => $lider,
                    'rol_id'                => 3, // Rol de líder
                    'grupo_pequeno_id'      => $grupoPequeno->id,
                    'estado_inscripcion_id' => 5, // Estado de inscripción líder
                    'info_adicional'        => 'Lider',
                ];
            }, $lideresAAgregar);

            // Agregar nuevas inscripciones de monitores
            $monitoresInscripciones = array_map(function ($monitor) use ($grupoPequeno) {
                return [
                    'usuario_id'            => $monitor,
                    'rol_id'                => 4, // Rol de monitor
                    'grupo_pequeno_id'      => $grupoPequeno->id,
                    'estado_inscripcion_id' => 6, // Estado de inscripción monitor
                    'info_adicional'        => 'Monitor',
                ];
            }, $monitoresAAgregar);

            // Combinar inscripciones de líderes y monitores y guardar
            $inscripciones = array_merge($lideresInscripciones, $monitoresInscripciones);
            $grupoPequeno->inscripciones()->createMany($inscripciones);

            DB::commit();

            return response()->json([
                "message" => "El Grupo pequeño fue actualizado exitosamente, con sus líderes y monitores!",
            ], 200);

        } catch (\Throwable $th) {
            DB::rollBack();

            return response()->json([
                "message" => $th->getMessage(),
                "server"  => "¡El Grupo pequeño no pudo ser actualizado, intente más tarde!",
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     */
    public function destroy($id) {

        try {

            $grupoPequeno = GrupoPequeno::whereId($id)
                ->withCount(['inscripciones as alumnos_contar' => function ($q) {$q->where('rol_id', 5);}])
                ->withCount(['inscripciones as lideres_contar' => function ($q) {$q->where('rol_id', 3);}])
                ->withCount(['inscripciones as monitores_contar' => function ($q) {$q->where('rol_id', 4);}])
                ->firstOrFail();
            if ($grupoPequeno->alumnos_contar > 0) {
                return response()->json(["message" => "", 'server' => '¡El horario del Grupo Pequeño no puede ser Eliminado porque tiene alumnos inscritos, deberás hacerte cargo de las alumnos primero!'], 400);
            }
            $grupoPequeno->inscripciones()->delete();
            $state = $grupoPequeno->delete();

            if ($state) {
                return response()->json(["message" => "El Horario del Grupo Pequeño fue eliminada exitosamente!"], 200);
            } else {
                return response()->json(["message" => "", 'server' => '¡El Horario del Grupo Pequeño no pudo ser eliminada, intente más tarde!'], 400);

            }
        } catch (\Throwable $th) {
            return response()->json(["message" => $th->getMessage(), 'server' => '¡El Horario del Grupo Pequeño no pudo ser eliminada, intente más tarde!'], 500);
        }
    }

    public function find(Request $request, $temporadasActivas = false) {
        $all = $request->all();
        Debug::info($all);

        $rol = session()->get('rol_id');

        $perPage     = trim($request->input('perPage', 20));
        $temporadas  = $request->input('temporadas', []);
        $curriculums = $request->input('curriculums', []);
        $ciclos      = $request->input('ciclos', []);
        $dias        = $request->input('dias', []);
        $nombre      = trim($request->input('nombre'));

        $idsCurriculum = Usuario::auth()->curriculums()->pluck('curriculums.id');
        if ($rol === RolHelper::$COORDINADOR && $idsCurriculum->count() == 0) {
            return collect([]);
        }

        $gruposPequenos =
        GrupoPequeno::with(
            [
                'ciclo'             => function ($q) {$q->select('id', 'nombre', 'curriculum_id');},
                'ciclo.curriculum'  => function ($q) {$q->select('id', 'nombre');},
                'temporada'         => function ($q) {$q->select('id', 'nombre', 'prefijo', 'activo');},
                'lideres.persona'   => function ($q) {$q->select('id', 'nombre', 'apellido');},
                'monitores.persona' => function ($q) {$q->select('id', 'nombre', 'apellido');},
            ]
        )->withCount('alumnos');

        if ($rol === RolHelper::$COORDINADOR) {
            $gruposPequenos = $gruposPequenos->whereHas('ciclo.curriculum',
                function ($q) use ($idsCurriculum) {$q->whereIn('curriculums.id', $idsCurriculum);});
        }
        if ($temporadasActivas) {
            $gruposPequenos = $gruposPequenos->whereHas('temporada',
                function ($query) {$query->where('activo', true);});
        }
        if (count($temporadas) > 0) {
            $gruposPequenos = $gruposPequenos->whereIn('temporada_id', $temporadas);
        }
        if (count($curriculums) > 0) {
            $gruposPequenos = $gruposPequenos->
                whereHas('ciclo.curriculum', function ($q) use ($curriculums) {
                $q->whereIn('nombre', $curriculums);
            });
        }
        if (count($ciclos) > 0) {
            $gruposPequenos = $gruposPequenos->
                whereHas('ciclo', function ($q) use ($ciclos) {
                $q->whereIn('nombre', $ciclos);
            });
        }
        if (count($dias) > 0) {
            $gruposPequenos = $gruposPequenos->whereIn('dia_curso', $dias);
        }
        $gruposPequenos = $gruposPequenos->orderBy('temporada_id', 'desc')->paginate($perPage);

        return $gruposPequenos;

    }
}
