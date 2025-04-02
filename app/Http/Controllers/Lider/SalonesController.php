<?php
namespace App\Http\Controllers\Lider;

use App\Helpers\AsistenciaHelper;
use App\Helpers\Debug;
use App\Http\Controllers\Controller;
use App\Models\Asistencia;
use App\Models\Curriculum;
use App\Models\GrupoPequeno;
use App\Models\Temporada;
use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class SalonesController extends Controller {

    public function index() {
        $temporadasId = Temporada::activo()->pluck('id')->toArray();
        $usuario      = Usuario::auth();

        $curriculums = Curriculum::whereHas('ciclos.gruposPequenos', function ($query) use ($temporadasId, $usuario) {
            $query->whereIn('temporada_id', $temporadasId)
                ->whereHas('lideres', function ($query) use ($usuario) {
                    $query->where('usuarios.id', $usuario->id);
                })
            ;
        })->get();
        Debug::infoJson($curriculums);
        return Inertia::render('Lider/misSalones', [
            'curriculums' => $curriculums,
        ]);
    }

    public function curriculum(String $idCryptCurriculum) {
        $id           = base64_decode($idCryptCurriculum);
        $usuario      = Usuario::auth();
        $temporadasId = Temporada::activo()->pluck('id')->values();
        $curriculum   = Curriculum::where('curriculums.id', $id)
            ->whereHas('ciclos.grupospequenos', function ($query) use ($temporadasId, $usuario) {
                $query->whereIn('temporada_id', $temporadasId)
                    ->whereHas('lideres', function ($query) use ($usuario) {
                        $query->where('usuarios.id', $usuario->id);
                    });
            })->first();

        if (! $curriculum) {return redirect()->route('mis-salones')->with(['error' => 'Curriculum no disponible o no existe!']);}

        $grupospequenos = GrupoPequeno::whereIn('temporada_id', $temporadasId)
            ->whereHas('lideres', function ($query) use ($usuario) {
                $query->where('usuarios.id', $usuario->id);
            })
            ->whereHas('ciclo', function ($query) use ($curriculum) {
                $query->where('curriculum_id', $curriculum->id);
            })
            ->with('ciclo')->get();

        Debug::infoJson($curriculum);
        return Inertia::render('Lider/salonesCiclos', [
            'curriculum'     => $curriculum,
            'grupospequenos' => $grupospequenos,
        ]);
    }
    public function misAlumnos(String $idCryptCurriculum, int $id) {
        $idCurriculum = base64_decode($idCryptCurriculum);
        $usuario      = Usuario::auth();
        $temporadasId = Temporada::activo()->pluck('id')->values();
        $grupopequeno = GrupoPequeno::whereId($id)
            ->with(
                'lideres', 'monitores',
                'inscripcionesAlumnos.usuario',
                'inscripcionesAlumnos.usuario.persona.region:id,nombre,pais_id',
                'inscripcionesAlumnos.usuario.persona.region.pais:id,nombre',
                'inscripcionesAlumnos.usuario.persona.nacionalidad:id,nombre',
                'inscripcionesAlumnos.usuario.persona.estadoCivil:id,estado',
                'inscripcionesAlumnos.estadoInscripcion',
                'temporada:id,prefijo',
                'ciclo:id,nombre,curriculum_id',
                'ciclo.curriculum:id,nombre'

            )
            ->whereIn('temporada_id', $temporadasId)
            ->whereHas('lideres', function ($query) use ($usuario) {$query->where('usuarios.id', $usuario->id);})
            ->whereHas('ciclo.curriculum', function ($query) use ($idCurriculum) {
                $query->where('curriculums.id', $idCurriculum);
            })
            ->first();
        if (! $grupopequeno) {
            return redirect()
                ->route('mis-salones.curriculum', ['idCryptCurriculum' => $idCryptCurriculum])
                ->with(['error' => 'Grupo pequeño no disponible o no existe!']);
        }
        $inscripciones = $grupopequeno->inscripcionesAlumnos;
        unset($grupopequeno->inscripcionesAlumnos);
        return Inertia::render('Lider/misAlumnos', [
            'grupopequeno'  => $grupopequeno,
            'inscripciones' => $inscripciones,
        ]);
    }

    public function update(Request $request, int $id) {

        $inscrito   = collect($request->input('inscrito', []))->pluck('id');
        $presente   = collect($request->input('presente', []))->pluck('id');
        $ausente    = collect($request->input('ausente', []))->pluck('id');
        $recuperado = collect($request->input('recuperado', []))->pluck('id');
        $noAplica   = collect($request->input('noAplica', []))->pluck('id');
        $otros      = collect($request->input('otros', []))->pluck('id');

        try {
            DB::beginTransaction();
            //code...
            Asistencia::whereIn('id', $inscrito)->update(['estado_asistencia_id' => AsistenciaHelper::$INSCRITO]);
            Asistencia::whereIn('id', $presente)->update(['estado_asistencia_id' => AsistenciaHelper::$PRESENTE]);
            Asistencia::whereIn('id', $ausente)->update(['estado_asistencia_id' => AsistenciaHelper::$AUSENTE]);
            Asistencia::whereIn('id', $recuperado)->update(['estado_asistencia_id' => AsistenciaHelper::$RECUPERADO]);
            Asistencia::whereIn('id', $noAplica)->update(['estado_asistencia_id' => AsistenciaHelper::$NO_APLICA]);

            $otros->each(function ($asistencia) {
                $asistencia = (object) $asistencia;
                Asistencia::find($asistencia->id)->update(['estado_asistencia_id' => $asistencia->estado_asistencia_id]);
            });
            DB::commit();
            return response()->json(["message" => "Asistencia actualizada correctamente!"], 200);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(["message" => "", "server" => "No se puedieron actualizar las asistencia, intente más tarde!"], 400);
        }

        // Debug::info($asistencias);

    }
}
