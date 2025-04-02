<?php
namespace App\Http\Controllers\Excel;

use App\Helpers\Debug; // Asegúrate de crear esta clase de exportación
use App\Helpers\RolHelper;
use App\Http\Controllers\Controller;
use App\Models\Ciclo;
use App\Models\Curriculum;
use App\Models\GrupoPequeno;
use App\Models\Usuario;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ExcelExportController extends Controller {

    private $msg = "No Hay datos disponibles";

    public function export() {
    }
    public function exportPersonas(Request $request) {
        $all = $request->all();
        Debug::info($all);

        $buscador = trim($request->input('buscador', ''));

        $usuarios = Usuario::
            join('personas as p', 'persona_id', 'p.id')
            ->join('generos as g', 'p.genero_id', 'g.id')
            ->join('estados_civiles as ec', 'p.estado_civil_id', 'ec.id')
            ->join('nacionalidades as n', 'p.nacionalidad_id', 'n.id')
            ->join('regiones as r', 'p.region_id', 'r.id')
            ->join('paises as pa', 'r.pais_id', 'pa.id');

        if ($buscador != '') {
            $usuarios = $usuarios->where(function ($q) use ($buscador) {
                $q->where('p.nombre', 'like', '%' . $buscador . '%')
                    ->orWhere('p.apellido', 'like', '%' . $buscador . '%')
                    ->orWhere('p.dni', 'like', '%' . $buscador . '%')
                    ->orWhere('p.telefono', 'like', '%' . $buscador . '%')
                    ->orWhere('p.ocupacion', 'like', '%' . $buscador . '%');
            });

        }
        foreach (['genero', 'estado_civil', 'nacionalidad', 'region'] as $field) {
            $value = $request->input($field, null);
            if (! is_null($value)) {
                $usuarios = $usuarios->where('p.' . $field . '_id', $value);
            }
        }
        $pais_id = $request->input('pais', null);
        if (! is_null($pais_id)) {
            $usuarios = $usuarios->where('pa.id', $pais_id);
        }
        $usuarios = $usuarios->select('email', 'p.nombre', 'p.apellido', 'p.telefono', 'p.ocupacion',
            'g.nombre as genero', 'ec.estado as estado civil', 'n.nombre as nacionalidad',
            'r.nombre as region', 'pa.nombre as país'
        )->get();
        $usuarios->makeHidden(['fullNombre', 'nombreCompleto', 'idCrypt', 'persona_id', 'persona']);

        if ($usuarios->count() == 0) {
            return response()->json(["message" => $this->msg], 400);
        }
        return Excel::download(new GenericExport($usuarios), 'usuarios.xlsx');
    }
    public function exportCurriculums(Request $request) {
        $data = Curriculum::select('nombre', 'libro', 'activo', 'descripcion', 'cantidad_clases as cantidad clases', 'cantidad_cupos as cantidad cupos')->get();
        $data->makeHidden(['idCrypt']);
        if ($data->count() == 0) {
            return response()->json(["message" => $this->msg], 400);
        }
        return Excel::download(new GenericExport($data), 'data.xlsx');
    }
    public function exportCiclos(Request $request) {
        $data = Ciclo::join('curriculums as c', 'curriculum_id', 'c.id')
            ->select('c.nombre as curriculum', 'ciclos.nombre as ciclo', 'ciclos.descripcion as ciclo descripción')
            ->get();
        $data->makeHidden(['idCrypt']);
        if ($data->count() == 0) {
            return response()->json(["message" => $this->msg], 400);
        }
        return Excel::download(new GenericExport($data), 'data.xlsx');
    }
    public function exportUsuariosRoles(Request $request) {
        $data = Usuario::
            join('personas as p', 'persona_id', 'p.id')
            ->leftJoin('usuario_roles as ur', 'ur.usuario_id', 'usuarios.id')
            ->leftJoin('roles as r', 'r.id', 'ur.rol_id')

            ->with('roles')->whereHas('roles', function ($query) {
            $query->whereIn('roles.id', [1, 2, 3, 4]);
        })
            ->select('email', 'p.nombre', 'p.apellido')
            ->selectRaw("GROUP_CONCAT(r.nombre SEPARATOR '-') AS nombres_roles")
            ->selectRaw('
        (SELECT COUNT(*) FROM usuario_curriculums WHERE usuario_curriculums.usuario_id = usuarios.id) AS curriculums a cargo')
            ->groupBy('usuarios.id', 'p.id')
            ->get();

        $data->makeHidden(['fullNombre', 'nombreCompleto', 'idCrypt', 'persona_id', 'persona']);

        if ($data->count() == 0) {
            return response()->json(["message" => $this->msg], 400);
        }
        return Excel::download(new GenericExport($data), 'data.xlsx');
    }

    public function exportGruposPequenoTemporada(Request $request) {

    }
    public function exportGruposPequeno(Request $request) {
        $all = $request->all();
        Debug::info($all);

        $rol = session()->get('rol_id');

        $temporadasActivas = $request->input('temporadasActivas');
        $temporadasActivas = filter_var($temporadasActivas, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
        $temporadas        = $request->input('temporadas', []);
        $curriculums       = $request->input('curriculums', []);
        $ciclos            = $request->input('ciclos', []);
        $dias              = $request->input('dias', []);

        $idsCurriculum = Usuario::auth()->curriculums()->pluck('curriculums.id');
        if ($rol === RolHelper::$COORDINADOR && $idsCurriculum->count() == 0) {
            return response()->json(["message" => $this->msg], 400);
        }

        $data = GrupoPequeno::
            join('ciclos', 'grupo_pequenos.ciclo_id', '=', 'ciclos.id')
            ->join('curriculums', 'ciclos.curriculum_id', '=', 'curriculums.id')
            ->join('temporadas', 'grupo_pequenos.temporada_id', '=', 'temporadas.id')
            ->leftJoin('inscripciones as lider_inscripciones', 'grupo_pequenos.id', '=', 'lider_inscripciones.grupo_pequeno_id')
            ->leftJoin('usuarios as lideres', 'lider_inscripciones.usuario_id', '=', 'lideres.id')
            ->leftJoin('personas as lideres_persona', 'lideres.persona_id', '=', 'lideres_persona.id')
            ->where('lider_inscripciones.rol_id', RolHelper::$LIDER)
            ->leftJoin('inscripciones as monitor_inscripciones', 'grupo_pequenos.id', '=', 'monitor_inscripciones.grupo_pequeno_id')
            ->leftJoin('usuarios as monitores', 'monitor_inscripciones.usuario_id', '=', 'monitores.id')
            ->leftJoin('personas as monitores_persona', 'monitores.persona_id', '=', 'monitores_persona.id')
            ->where('monitor_inscripciones.rol_id', RolHelper::$MONITOR);

        if ($rol === RolHelper::$COORDINADOR) {
            $data = $data->whereIn('curriculums.id', $idsCurriculum);
        }
        if ($temporadasActivas) {
            $data = $data->where('temporadas.activo', true);
        }
        if (count($temporadas) > 0) {
            $data = $data->whereIn('temporada_id', $temporadas);
        }
        if (count($curriculums) > 0) {
            $data = $data->whereIn('curriculums.nombre', $curriculums);

        }
        if (count($ciclos) > 0) {
            $data = $data->whereIn('ciclos.nombre', $ciclos);
        }
        if (count($dias) > 0) {
            $data = $data->whereIn('dia_curso', $dias);
        }

        $data = $data->select(
            'grupo_pequenos.id as id',
            'curriculums.nombre as curriculum_nombre',
            'ciclos.nombre as ciclo_nombre',
            'grupo_pequenos.dia_curso',
            'grupo_pequenos.hora_inicio',
            'grupo_pequenos.hora_fin',
            // 'temporadas.nombre as temporada_nombre',
            'temporadas.prefijo as temporada_prefijo',
            // 'grupo_pequenos.info_adicional',
            // 'nombre_curso',
            'lideres_persona.nombre as lider_nombre',
            'lideres_persona.apellido as lider_apellido',
            'monitores_persona.nombre as monitor_nombre',
            'monitores_persona.apellido as monitor_apellido',
            'grupo_pequenos.activo_inscripcion',
        )
            ->orderBy('temporadas.id', 'desc')
            ->orderBy('curriculum_nombre')
            ->orderBy('ciclo_nombre')
            ->get();
        $data->makeHidden(['hora', 'horaInicioFormat', 'horaFinFormat', 'idCrypt']);

        if ($data->count() == 0) {
            return response()->json(["message" => $this->msg], 400);
        }
        return Excel::download(new GenericExport($data), 'data.xlsx');
    }
}
