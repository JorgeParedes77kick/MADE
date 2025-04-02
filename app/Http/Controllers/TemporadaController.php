<?php

namespace App\Http\Controllers;

use App\Helpers\AsistenciaHelper;
use App\Helpers\Debug;
use App\Helpers\InscripcionHelper;
use App\Helpers\RolHelper;
use App\Http\Requests\TemporadaRequest;
use App\Models\Asistencia;
use App\Models\Inscripcion;
use App\Models\Temporada;
use Carbon\Carbon;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class TemporadaController extends Controller {
    /**
     * Display a listing of the resource.
     *
     */
    public function index(Request $request) {

        $temporadas = Temporada::orderBy('prefijo', 'desc')->get();
        return Inertia::render('Temporadas/index', [
            'temporadas' => $temporadas,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     */
    public function create() {
        return Inertia::render('Temporadas/form', [
            'action' => 'create',
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     */
    public function store(TemporadaRequest $request) {
        try {
            DB::beginTransaction();

            // Obtener todos los datos del request
            $input = $request->all();

            // Procesar las fechas de inicio, cierre y extensión
            $fechaInicio = $this->dateWeekToCarbon($request->input('fecha_inicio_w'));
            $fechaFin = $this->dateWeekToCarbon($request->input('fecha_cierre_w'));

            // Si no hay fecha de extensión, establecer una predeterminada
            $fecha_extension = $request->input('fecha_extension_w', '1900-W01') ?? '1900-W01';
            $fechaExtension = $this->dateWeekToCarbon($fecha_extension);
            // Definir las fechas de inicio y cierre en el input
            $input['fecha_inicio'] = $fechaInicio->startOfWeek()->format('Y-m-d');
            $input['fecha_cierre'] = $fechaFin->endOfWeek()->format('Y-m-d');
            if ($fecha_extension !== '1900-W01') {
                $input['fecha_extension'] = $fechaExtension->endOfWeek()->format('Y-m-d');
            }

            // Verificar si la fecha de extensión es mayor a la fecha de cierre
            $fin = $fechaExtension->isAfter($fechaFin) ? $fechaExtension : $fechaFin;

            // Inicializar semanas
            $weeks = $this->generateWeeks($fechaInicio, $fin, $fechaFin);

            $temporada = Temporada::create($input);
            $temporada->semanas()->createMany($weeks);
            DB::commit();

            return response()->json(["message" => "La Temporada fue creada exitosamente!"], 200);

        } catch (\Throwable $th) {
            DB::rollBack();

            return response()->json([
                'server' => '¡La Temporada no pudo ser creada, intente más tarde!',
                'message' => $th->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     */
    public function show($id) {
        $temporada = Temporada::whereId($id)->with('semanas')->first();
        return Inertia::render('Temporadas/form', [
            'action' => 'show',
            'temporada' => $temporada,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     */
    public function edit($id) {
        $temporada = Temporada::whereId($id)->with('semanas', function ($q) {$q->orderBy('fecha_inicio', 'desc');})->first();
        // $semanaIds = $temporada->semanas()->pluck('id')->filter()->all();
        // foreach ($semanaIds as $i => $week) {
        //     Debug::info([
        //         'id' => $semanaIds[$i] ?? null, // Buscar por ID si existe
        //         $week, // Actualizar o crear con los datos
        //     ]);
        // }
        return Inertia::render('Temporadas/form', [
            'action' => 'edit',
            'temporada' => $temporada,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     */
    public function update(TemporadaRequest $request, $id) {
        try {
            DB::beginTransaction();

            // Obtener todos los datos del request
            $input = $request->all();

            // Procesar las fechas de inicio, cierre y extensión
            $fechaInicio = $this->dateWeekToCarbon($request->input('fecha_inicio_w'));
            $fechaFin = $this->dateWeekToCarbon($request->input('fecha_cierre_w'));

            // Si no hay fecha de extensión, establecer una predeterminada
            $fecha_extension = $request->input('fecha_extension_w', '1900-W01') ?? '1900-W01';
            $fechaExtension = $this->dateWeekToCarbon($fecha_extension);
            // Definir las fechas de inicio y cierre en el input
            $input['fecha_inicio'] = $fechaInicio->startOfWeek()->format('Y-m-d');
            $input['fecha_cierre'] = $fechaFin->endOfWeek()->format('Y-m-d');
            if ($fecha_extension !== '1900-W01') {
                $input['fecha_extension'] = $fechaExtension->endOfWeek()->format('Y-m-d');
            }
            // Verificar si la fecha de extensión es mayor a la fecha de cierre
            $fin = $fechaExtension->isAfter($fechaFin) ? $fechaExtension : $fechaFin;
            // Inicializar semanas
            $weeks = $this->generateWeeks($fechaInicio, $fin, $fechaFin);

            $temporada = Temporada::find($id);
            $semanaIds = $temporada->semanas()->limit(count($weeks))->pluck('id')->filter()->all();

            Debug::infoJson($semanaIds);
            $temporada->update($input);

            foreach ($weeks as $i => $week) {
                $temporada->semanas()->updateOrCreate(
                    ['id' => $semanaIds[$i] ?? null], // Buscar por ID si existe
                    $week // Actualizar o crear con los datos
                );
            }
            $semanasBorrar = $temporada->semanas()->whereNotIn('id', $semanaIds);
            $semanasId = $semanasBorrar->pluck('id');
            Asistencia::whereIn('semana_id', $semanasId)->delete();
            if ($semanasBorrar->count() > 0) {
                $semanasBorrar->delete();
            }

            DB::commit();

            return response()->json(["message" => "La Temporada fue actualizada exitosamente!"], 200);

        } catch (\Throwable $th) {
            DB::rollBack();

            return response()->json([
                'server' => '¡La Temporada no pudo ser actualizada, intente más tarde!',
                'message' => $th->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     */
    public function destroy($id) {
        $temporada = Temporada::find($id);
        $semanasId = $temporada->semanas()->pluck('id');
        Asistencia::whereIn('semana_id', $semanasId)->delete();
        $temporada->semanas()->delete();
        $state = $temporada->delete();

        if ($state) {
            return response()->json(["message" => "La Temporada fue eliminada exitosamente!"], 200);
        } else {
            return response()->json(["message" => [], 'server' => '¡La Temporada no pudo ser eliminada, intente más tarde!'], 500);
        }
    }

    public function toggleActivo($id) {
        $temporada = Temporada::find($id);
        $temporada->activo = !$temporada->activo;
        $state = $temporada->save();

        if ($state) {
            return response()->json(["message" => "La Temporada fue actualizada exitosamente!", "temporada" => $temporada], 200);
        } else {
            return response()->json(["message" => [], 'server' => '¡La Temporada no pudo ser actualizada, intente más tarde!'], 500);
        }
    }
    public function toggleInscripcion($id) {
        $temporada = Temporada::find($id);
        $temporada->activo_inscripcion = !$temporada->activo_inscripcion;
        $state = $temporada->save();

        if ($state) {
            return response()->json(["message" => "La Temporada fue actualizada exitosamente!", "temporada" => $temporada], 200);
        } else {
            return response()->json(["message" => [], 'server' => '¡La Temporada no pudo ser actualizada, intente más tarde!'], 500);
        }
    }
    public function checkDelete($id) {
        $temporada = Temporada::find($id);
        $gruposId = $temporada->grupospequenos()->pluck('id');
        $inscripciones = Inscripcion::whereIn('grupo_pequeno_id', $gruposId)->where('rol_id', RolHelper::$ALUMNO)->count();
        $state = (object) [
            'isDelete' => $gruposId->count() == 0,
            'withGrupos' => $gruposId->count() > 0,
            'withAlumnos' => $inscripciones > 0,
        ];
        return response()->json($state);

    }
    private function dateWeekToCarbon(string $date) {
        list($year, $week) = explode('-W', $date);
        $d = (new DateTime())->setISODate($year, $week);
        $d->setISODate($year, $week);
        return new Carbon($date);
    }
    private function getWeekDates($dateCarbon, $offset = 0) {
        $offsetWeek = $dateCarbon->addWeeks($offset);
        $monday = $offsetWeek->copy()->startOfWeek();
        $sunday = $offsetWeek->copy()->endOfWeek();
        $fecha_inicio = $monday->format('Y-m-d');
        $fecha_fin = $sunday->format('Y-m-d');
        return (Object) [
            'inicio' => $fecha_inicio,
            'fin' => $fecha_fin,
        ];

    }
    private function generateWeeks($fechaInicio, $fechaFin, $fechaCierre) {
        $weeks = [];

        while ($fechaInicio->isBefore($fechaFin)) {
            $weekDates = $this->getWeekDates($fechaInicio);

            $weeks[] = [
                'fecha_inicio' => $weekDates->inicio,
                'fecha_fin' => $weekDates->fin,
                'es_extension' => $fechaInicio->isAfter($fechaCierre),
            ];

            $fechaInicio->addWeek(); // Mover al siguiente lunes
        }

        return $weeks;
    }

    public function calificarAlumnos(Request $request) {
        $temporada_id = $request->input('temporada_id');
        $ids_temporadas = $temporada_id ? collect([$temporada_id]) : Temporada::activo()->pluck('id');
        if ($ids_temporadas->count() == 0) {
            return response()->json(["server" => 'No hay Temporada activa para calificar a sus alumnos', 'message' => []], 400);

        }
        try {
            DB::beginTransaction();
            // Obtener reprobados
            $reprobados = Inscripcion::alumno()
                ->whereHas('grupoPequeno', function ($q) use ($ids_temporadas) {
                    $q->whereIn('temporada_id', $ids_temporadas);
                })
                ->withCount(['semanas' => function ($q) {
                    $q->where('estado_asistencia_id', AsistenciaHelper::$AUSENTE);
                }])
                ->having('semanas_count', '>=', 3)
                ->pluck('id'); // Obtener solo los IDs de los reprobados

            // Actualizar estado de reprobados
            if ($reprobados->isNotEmpty()) {
                Inscripcion::whereIn('id', $reprobados)
                    ->update(['estado_inscripcion_id' => InscripcionHelper::$REPROBADO]);
            }

            // Obtener inscritos
            $inscritos = Inscripcion::alumno()
                ->whereNotIn('id', $reprobados)
                ->whereHas('grupoPequeno', function ($q) use ($ids_temporadas) {
                    $q->whereIn('temporada_id', $ids_temporadas);
                })
                ->withCount(['semanas' => function ($q) {
                    $q->where('estado_asistencia_id', AsistenciaHelper::$INSCRITO);
                }])
                ->having('semanas_count', '>=', 3)
                ->pluck('id'); // Obtener solo los IDs de los inscritos

            // Actualizar estado de inscritos
            if ($inscritos->isNotEmpty()) {
                Inscripcion::whereIn('id', $inscritos)
                    ->update(['estado_inscripcion_id' => InscripcionHelper::$INSCRITO]);
            }

            // Combinar IDs de reprobados e inscritos
            $ids = $reprobados->merge($inscritos);

            // Obtener aprobados
            $aprobados = Inscripcion::alumno()
                ->whereNotIn('id', $ids)
                ->whereHas('grupoPequeno', function ($q) use ($ids_temporadas) {
                    $q->whereIn('temporada_id', $ids_temporadas);
                })
                ->withCount(['semanas' => function ($q) {
                    $q->whereIn('estado_asistencia_id', [AsistenciaHelper::$PRESENTE, AsistenciaHelper::$RECUPERADO]);
                }])
                ->having('semanas_count', '>=', 3)
                ->pluck('id'); // Obtener solo los IDs de los aprobados

            // Actualizar estado de aprobados
            if ($aprobados->isNotEmpty()) {
                Inscripcion::whereIn('id', $aprobados)
                    ->update(['estado_inscripcion_id' => InscripcionHelper::$APROBADO]);
            }
            DB::commit();
            return response()->json(['message' => 'La Calificación de las actualizadas se hizo correctamente.']);

        } catch (\Throwable $th) {
            //throw $th;
            DB::rollBack();
            return response()->json(["message" => [], 'server' => '¡La calificación de las Inscripciones fallo, intente más tarde!'], 500);

        }
    }
}
