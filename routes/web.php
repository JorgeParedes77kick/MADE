<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
 */

/**
 * Rutas Inicial
 */
Route::get('/', function () {
    if (Auth::check()) {
        return redirect()->route('home');
    }
    return Inertia::render('Login/LoginPage');
});
/**
 * Rutas Registro Usuario
 */
// Route::get('/gender/list', [App\Http\Controllers\GeneroController::class, 'list'])->name('gender.list');
// Route::get('/civilStatus/list', [App\Http\Controllers\EstadoCivilController::class, 'list'])->name('civilStatus.list');
// Route::get('/nationality/list', [App\Http\Controllers\NacionalidadController::class, 'list'])->name('nationality.list');
// Route::get('/country/list', [App\Http\Controllers\PaisController::class, 'list'])->name('country.list');
// Route::get('/region/list/{country}', [App\Http\Controllers\RegionController::class, 'list'])->name('region.list');
Route::post('/persona/store', [App\Http\Controllers\PersonaController::class, 'store'])->name('persona.store');
Route::delete('persona/{persona_id}/delete', [App\Http\Controllers\PersonaController::class, 'destroy'])->name('persona.destroy');
Route::get('/user/validate-token/{email}/{token}', [App\Http\Controllers\UsuarioController::class, 'canResetPass'])->name('user.validate-token');

/**
 * Rutas Usuario Autorizado
 */
Route::middleware(['auth'])->group(function () {
    Route::post('/roles/rolApply', [App\Http\Controllers\RolController::class, 'applyRol'])->name('roles.rolApply');

    Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
    Route::get('/horario/{idCrypt}', [App\Http\Controllers\Alumno\InscripcionController::class, 'curriculum'])->name('horario.curriculum');

    Route::middleware(['checkRole'])->group(function () {
        Route::post('/horario/inscripcion', [App\Http\Controllers\Alumno\InscripcionController::class, 'inscribir'])->name('horario.inscripcion.store');
        Route::delete('/horario/inscripcion/{idCrypt}', [App\Http\Controllers\Alumno\InscripcionController::class, 'desinscribir'])->name('horario.inscripcion.delete');

        //ALUMNO
        Route::get('/mis-cursos', [App\Http\Controllers\Alumno\InscripcionController::class, 'cursos'])->name('mis-cursos');
        Route::resource('/mis-recursos', App\Http\Controllers\Alumno\RecursosController::class)->only(['index', 'show']);
        //LIDER
        Route::resource('/mis-salones', App\Http\Controllers\Lider\SalonesController::class)->only(['index', 'update']);
        Route::get('/mis-salones/{idGrupo}/asistencia', [App\Http\Controllers\AsistenciaController::class, 'misSalonesAsistencia'])->name('mis-salones.asistencia');
        Route::get('/mis-salones/{idCryptCurriculum}/{id}', [App\Http\Controllers\Lider\SalonesController::class, 'misAlumnos'])->name('mis-salones.grupo');
        Route::get('/mis-salones/{idCryptCurriculum}', [App\Http\Controllers\Lider\SalonesController::class, 'curriculum'])->name('mis-salones.curriculum');

        Route::resource('/roles', App\Http\Controllers\RolController::class);
        Route::resource('/estados-asistencia', App\Http\Controllers\EstadoAsistenciaController::class);
        Route::resource('/estados-inscripcion', App\Http\Controllers\EstadoInscripcionController::class);
        Route::resource('/curriculums', App\Http\Controllers\CurriculumController::class)->except(['update']);
        Route::post('curriculums/{curriculum}/update', [App\Http\Controllers\CurriculumController::class, 'update'])->name('curriculums.update');
        Route::resource('/restricciones', App\Http\Controllers\RestriccionController::class);
        Route::resource('/adicionales-curriculum', App\Http\Controllers\AdicionalController::class)->except(['update', 'destroy']);
        Route::resource('/ciclos', App\Http\Controllers\CicloController::class);
        Route::resource('/recursos', App\Http\Controllers\RecursoController::class);
        Route::resource('/usuarios-equipo', App\Http\Controllers\UsuarioRolesController::class)->except(['destroy']);

        Route::resource('/inscripcion', App\Http\Controllers\InscripcionController::class)->except(['show', 'create', 'destroy']);
        Route::get('inscripcion/find-email/{email}', [App\Http\Controllers\InscripcionController::class, 'findEmail'])->name('inscripcion.find-email');
        Route::post('inscripcion/find-lideres', [App\Http\Controllers\InscripcionController::class, 'findGrupos'])->name('inscripcion.find-lideres');
        Route::post('inscripcion/find-grupos', [App\Http\Controllers\InscripcionController::class, 'findGrupos'])->name('inscripcion.find-grupos');

        Route::get('/grupos-pequenos/horario', [App\Http\Controllers\GrupoPequenoController::class, 'horario'])->name('grupos-pequenos.horarios');
        Route::resource('/grupos-pequenos', App\Http\Controllers\GrupoPequenoController::class);
        Route::get('/asistencias/grupo/{idGrupo}', [App\Http\Controllers\AsistenciaController::class, 'getAsistenciaGrupo'])->name('asistencias.grupo');
        Route::resource('/asistencias', App\Http\Controllers\AsistenciaController::class)->only(['index', 'show']);
        Route::get('/mi-perfil', [App\Http\Controllers\PersonaController::class, 'perfil'])->name('mi-perfil');
        Route::post('/persona/find', [App\Http\Controllers\PersonaController::class, 'find'])->name('personas.find');
        Route::resource('/personas', App\Http\Controllers\PersonaController::class)->only(['index', 'edit', 'update']);
        Route::resource('/temporadas', App\Http\Controllers\TemporadaController::class);
        Route::post('/calificar-alumnos', [App\Http\Controllers\TemporadaController::class, 'calificarAlumnos'])->name('calificar');

        // Excel
        Route::get('/exportar/usuarios', [App\Http\Controllers\Excel\ExcelExportController::class, 'exportPersonas'])->name('exportar.usuarios');
        Route::get('/exportar/curriculums', [App\Http\Controllers\Excel\ExcelExportController::class, 'exportCurriculums'])->name('exportar.curriculums');
        Route::get('/exportar/ciclos', [App\Http\Controllers\Excel\ExcelExportController::class, 'exportCiclos'])->name('exportar.ciclos');
        Route::get('/exportar/usuariosRoles', [App\Http\Controllers\Excel\ExcelExportController::class, 'exportUsuariosRoles'])->name('exportar.usuarios-roles');
        Route::get('/exportar/gruposPequenos', [App\Http\Controllers\Excel\ExcelExportController::class, 'exportGruposPequeno'])->name('exportar.grupos-pequenos');
        // Route::get('/exportar/usuarios', [App\Http\Controllers\Excel\ExcelExportController::class, 'exportPersonas'])->name('exportar.usuarios');
    });

    Route::resource('/donaciones', App\Http\Controllers\DonacionController::class);
});

/**
 * Rutas Usuario autorizado y rol Administrador
 */
Route::middleware(['auth',
    'super.admin',
])->group(function () {

    Route::resource('/menu', App\Http\Controllers\MenuController::class);
    Route::resource('/rol-menu', App\Http\Controllers\RolMenuController::class);
    Route::get('/dashboard', [App\Http\Controllers\DashboardController::class, 'index'])->name('dashboard');
    Route::get('/home/no-filter', [App\Http\Controllers\HomeController::class, 'index'])->name('home.no-filter');
    Route::resource('/globals', App\Http\Controllers\GlobalController::class)->only(['index', 'update']);
    Route::post('/temporadas/${id}/toggleActivo', [App\Http\Controllers\TemporadaController::class, 'toggleActivo'])->name('temporadas.toggleActivo');
    Route::post('/temporadas/${id}/toggleInscripcion', [App\Http\Controllers\TemporadaController::class, 'toggleInscripcion'])->name('temporadas.toggleInscripcion');
    Route::post('/temporadas/${id}/checkDelete', [App\Http\Controllers\TemporadaController::class, 'checkDelete'])->name('temporadas.checkDelete');
});
