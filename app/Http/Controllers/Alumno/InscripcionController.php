<?php
namespace App\Http\Controllers\Alumno;

use App\Helpers\Debug;
use App\Helpers\GlobalHelper;
use App\Helpers\InscripcionHelper;
use App\Helpers\RolHelper;
use App\Http\Controllers\Controller;
use App\Models\Asistencia;
use App\Models\Ciclo;
use App\Models\Curriculum;
use App\Models\GlobalProject;
use App\Models\GrupoPequeno;
use App\Models\Inscripcion;
use App\Models\Requisito;
use App\Models\Restriccion;
use App\Models\Semana;
use App\Models\Temporada;
use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class InscripcionController extends Controller {

    public function curriculum(String $idCrypt) {
        $id           = base64_decode($idCrypt);
        $temporadas   = Temporada::activo()->get();
        $temporadasId = $temporadas->pluck('id');

        $curriculum = Curriculum::where('curriculums.id', $id)->activo()
            ->with('ciclos', function ($query) use ($temporadasId) {
                $query->withHorarios($temporadasId)->whereHasHorarios($temporadasId);
            })
            ->whereHas('ciclos.grupospequenos', function ($query) use ($temporadasId) {
                $query->whereIn('temporada_id', $temporadasId);
            })
            ->first();

        // if (!$curriculum) {return redirect()->route('home')->with(['error' => 'Curriculum no disponible o no existe!']);}

        $parejasId           = Restriccion::parejas()->pluck('curriculum_id')->toArray();
        $curriculum->parejas = in_array($curriculum->id, $parejasId);

        // manejar el caso en que se encuentra un currículum activo con ese nombre
        Debug::infoJson($curriculum);
        return Inertia::render('Alumno/inscripcion', [
            'curriculum' => $curriculum,
            'temporadas' => $temporadas,

        ]);
    }

    public function inscribir(Request $request) {
        $usuario = Usuario::auth();

        // Obtener los parámetros necesarios del request
        $temporada_id = $request->input('temporada_id');

        // Obtener los valores globales para límites de inscripciones
        $globales = GlobalProject::whereIn('id', [GlobalHelper::$GRUPOS_POR_USUARIO, GlobalHelper::$INSCRIPCION_POR_GRUPO])
            ->get()
            ->pluck('castValor', 'id');

        $inscripcionesEnGrupo = $globales[GlobalHelper::$INSCRIPCION_POR_GRUPO] ?? 0;

        $retorno               = $this->validacionInscripcion($request, $usuario->id);
        $retornoPreInscripcion = $this->validacionPreInscripcion($request, $usuario->id);

        if (! $retorno->status) {
            return $retorno->response;
        }

        $grupo = $retorno->grupo;
        if ($retornoPreInscripcion->status) {
            $grupo = $retornoPreInscripcion->grupo;
        }

        // Iniciar transacción
        try {
            DB::beginTransaction();

            // Crear la inscripción
            $inscripcion = Inscripcion::create([
                'usuario_id'            => $usuario->id,
                'rol_id'                => RolHelper::$ALUMNO,
                'grupo_pequeno_id'      => $grupo->id,
                'estado_inscripcion_id' => InscripcionHelper::$INSCRITO,
                'info_adicional'        => '',
            ]);

            // Obtener las semanas de la temporada (solo los IDs)
            $semanas = Semana::where('temporada_id', $temporada_id)->pluck('id');
            // Sincronizar las semanas con la inscripción, si existen semanas
            $semanas->each(function ($semana_id) use ($inscripcion) {
                Asistencia::create(['semana_id' => $semana_id, 'inscripcion_id' => $inscripcion->id, 'estado_asistencia_id' => InscripcionHelper::$INSCRITO]);
            });
            //Si ahora llega al maximo;
            if ($grupo->alumnos_count == $inscripcionesEnGrupo - 1) {
                $grupo->update(['activo_inscripcion' => false]);
            }

            DB::commit();
            return response()->json(['message' => 'Inscripción realizada con éxito.'], 200);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'server'  => '¡Error al realizar la inscripción, intente más tarde!',
                'message' => $th->getMessage(),
            ], 500);
        }
    }

    public function cursos() {
        $usuario      = Usuario::auth();
        $temporadasId = Temporada::activo()->pluck('id')->toArray();

        $inscripciones = Inscripcion::where('usuario_id', $usuario->id)->where('rol_id', RolHelper::$ALUMNO)
            ->whereHas('grupoPequeno', function ($query) use ($temporadasId) {
                $query->whereIn('temporada_id', $temporadasId);
            })
            ->with(
                'grupoPequeno.ciclo:id,nombre,curriculum_id',
                'grupoPequeno.ciclo.curriculum:id,nombre',
                'grupoPequeno.temporada:id,prefijo',
                'estadoInscripcion:id,estado'
            )
            ->get();

        return Inertia::render('Alumno/misCursos', [
            'inscripciones' => $inscripciones,
        ]);
    }

    public function desinscribir(String $idCrypt) {
        $id          = base64_decode($idCrypt);
        $inscripcion = Inscripcion::find($id);
        if (! $inscripcion) {
            return response()->json(['server' => 'No existe esta inscripción'], 400);
        }
        try {
            DB::beginTransaction();
            $inscripcion->asistencias()->delete();
            $state = $inscripcion->delete();
            DB::commit();
            if ($state) {
                return response()->json(["message" => "La inscripción fue eliminada exitosamente!"], 200);
            } else {
                return response()->json(["message" => '', 'server' => '¡La inscripción no pudo ser eliminada, intente más tarde!'], 400);
            }
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'server'  => '¡La inscripción no pudo ser eliminada, intente más tarde!',
                'message' => $th->getMessage(),
            ], 500);
        }
    }

    function validacionInscripcion(Request $request, $usuario_id) {
        // Obtener los valores globales para límites de inscripciones
        $globales = GlobalProject::whereIn('id', [GlobalHelper::$GRUPOS_POR_USUARIO, GlobalHelper::$INSCRIPCION_POR_GRUPO])
            ->get()
            ->pluck('castValor', 'id');
        $inscripcionesMaximas = $globales[GlobalHelper::$GRUPOS_POR_USUARIO] ?? 0;
        $inscripcionesEnGrupo = $globales[GlobalHelper::$INSCRIPCION_POR_GRUPO] ?? 0;

        // Obtener los parámetros necesarios del request
        $temporada_id = $request->input('temporada_id');
        $ciclo_id     = $request->input('ciclo_id');
        $dia_curso    = $request->input('dia_curso');
        $hora_inicio  = $request->input('hora_inicio');
        $hora_fin     = $request->input('hora_fin');

        // Obtener las inscripciones del usuario en la temporada enviada
        // Verificar si el usuario ya alcanzó el número máximo de inscripciones permitidas
        $inscripcionesUsuario = Inscripcion::where('usuario_id', $usuario_id)
            ->whereHas('grupoPequeno', function ($query) use ($temporada_id) {
                $query->where('temporada_id', $temporada_id);
            })->where('rol_id', RolHelper::$ALUMNO)
            ->count();

        if ($inscripcionesUsuario >= $inscripcionesMaximas) {
            return (object) ['status' => false, 'response' => response()->json(['server' => 'Has alcanzado el límite de inscripciones permitidas.'], 400)];
        }

        // Valida si ya estas inscrito en el mismo ciclo
        // Validar si ya estás inscrito en el mismo ciclo
        $preExistencia = Inscripcion::where('usuario_id', $usuario_id)
            ->whereHas('grupoPequeno', function ($query) use ($temporada_id, $ciclo_id) {
                $query->where('temporada_id', $temporada_id)
                    ->where('ciclo_id', $ciclo_id);
            })
            ->where('rol_id', RolHelper::$ALUMNO)
            ->exists(); // Cambiado a exists() para mejorar rendimiento

        if ($preExistencia) {
            return (object) ['status' => false, 'response' => response()->json(['server' => 'Ya te has inscrito en este grupo pequeño'], 400)];
        }

        // Validar si ya aprobaste este ciclo previamente
        $cursoAprobado = Inscripcion::where('usuario_id', $usuario_id)
            ->whereHas('grupoPequeno', function ($query) use ($ciclo_id) {
                $query->where('ciclo_id', $ciclo_id);
            })
            ->where('rol_id', RolHelper::$ALUMNO)
            ->where('estado_inscripcion_id', InscripcionHelper::$APROBADO)
            ->exists(); // Cambiado a exists() para mejorar rendimiento

        if ($cursoAprobado) {
            return (object) ['status' => false, 'response' => response()->json(['server' => 'Ya aprobaste este curso previamente'], 400)];
        }

        // Valida que tengas todos los prerequisitos de ciclos
        $idsCicloRequisitos    = Requisito::where('ciclo_id', $ciclo_id)->orderBy('ciclo_pre_id', 'asc')->pluck('ciclo_pre_id');
        $incripcionesAprobadas = Inscripcion::join('grupo_pequenos as grupo', 'grupo_pequeno_id', 'grupo.id')
            ->whereIn('ciclo_id', $idsCicloRequisitos)
            ->where('usuario_id', $usuario_id)
            ->where('estado_inscripcion_id', InscripcionHelper::$APROBADO)
            ->where('rol_id', RolHelper::$ALUMNO)
            ->select('ciclo_id')
            ->orderBy('ciclo_id', 'asc')->distinct()->pluck('ciclo_id');
        if ($idsCicloRequisitos->count() !== $incripcionesAprobadas->count()) {
            $requisitos = Ciclo::join('curriculums as c', 'curriculum_id', 'c.id')
                ->select('ciclos.nombre as nombre_ciclo', 'c.nombre as nombre_curriculum')
                ->whereIn('ciclos.id', $idsCicloRequisitos)->get();
            return (object) ['status' => false, 'response' => response()->json(['server' => 'No cumples con todos los pre requisitos para inscribirte en este ciclo', 'requisitos' => $requisitos], 400)];
        }
        // Indica a que Grupo de puede inscribir
        // Buscar los grupos disponibles que coincidan con los parámetros
        $grupo = GrupoPequeno::where('temporada_id', $temporada_id)
            ->where('ciclo_id', $ciclo_id)
            ->where('dia_curso', $dia_curso)
            ->where('hora_inicio', $hora_inicio)
            ->where('hora_fin', $hora_fin)
            ->activo()
            ->withCount('alumnos')
            ->having('alumnos_count', '<', $inscripcionesEnGrupo)
            ->orderBy('alumnos_count', 'asc')
            ->first();

        // Si no hay grupos disponibles, retornar una respuesta
        if (! $grupo) {
            return (object) ['status' => false, 'response' => response()->json(['server' => 'No hay inscripciones disponibles en este grupo.'], 400)];
        }
        return (object) ['status' => true, 'grupo' => $grupo];

    }

    function validacionPreInscripcion(Request $request, $usuario_id) {
        // Obtener los valores globales para límites de inscripciones
        $globales = GlobalProject::whereIn('id', [GlobalHelper::$GRUPOS_POR_USUARIO, GlobalHelper::$INSCRIPCION_POR_GRUPO])
            ->get()
            ->pluck('castValor', 'id');
        $inscripcionesMaximas = $globales[GlobalHelper::$GRUPOS_POR_USUARIO] ?? 0;
        $inscripcionesEnGrupo = $globales[GlobalHelper::$INSCRIPCION_POR_GRUPO] ?? 0;

        // Obtener los parámetros necesarios del request
        $temporada_id = $request->input('temporada_id');
        $ciclo_id     = $request->input('ciclo_id');
        $dia_curso    = $request->input('dia_curso');
        $hora_inicio  = $request->input('hora_inicio');
        $hora_fin     = $request->input('hora_fin');

        $temporada_Ant = Temporada::where('id', '<', $temporada_id)->max('id');
        $ciclo         = Ciclo::where('id', $ciclo_id)->with('curriculum:id,ciclo_id')->first();

        $ciclo_ant = Ciclo::where('curriculum_id', $ciclo->curriculum->id)->whereHas('dependientes', function ($query) use ($ciclo_id) {
            $query->where('ciclo_id', $ciclo_id);
        })->first();

        // Obtener las inscripciones del usuario en la temporada enviada
        $inscripcionAnterior = Inscripcion::where('usuario_id', $usuario_id)
            ->whereHas('grupoPequeno', function ($query) use ($temporada_Ant, $ciclo_ant) {
                $query->where('temporada_id', $$temporada_Ant->id)
                    ->where('ciclo_id', $ciclo_ant->id);
            })->where('rol_id', RolHelper::$ALUMNO)->first();

        if (! $inscripcionAnterior) {
            return (object) ['status' => false, 'response' => response()->json(['server' => 'No tienes inscripciones en la temporada anterior.'], 400)];
        }
        $lideres = $inscripcionAnterior->grupoPequeno->lideres->pluck('id');
        $grupo   = GrupoPequeno::where('temporada_id', $temporada_id)->where('ciclo_id', $ciclo_id)
            ->where('ciclo_id', $ciclo_id)
            ->where('dia_curso', $dia_curso)
            ->where('hora_inicio', $hora_inicio)
            ->where('hora_fin', $hora_fin)
            ->activo()
            ->whereHas('lideres', function ($q) use ($lideres) {
                $q->whereIn('usuario_id', $lideres);
            })
            ->withCount('alumnos')
            ->having('alumnos_count', '<', $inscripcionesEnGrupo)
            ->first();

        if (! $grupo) {
            return (object) ['status' => false, 'response' => response()->json(['server' => 'No hay inscripciones disponibles en este grupo.'], 400)];
        }
        return (object) ['status' => true, 'grupo' => $grupo];
    }

}
