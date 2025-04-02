<?php

namespace App\Http\Controllers;

use App\Helpers\RolHelper;
use App\Models\Curriculum;
use App\Models\Rol;
use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class UsuarioRolesController extends Controller {
    /**
     * Display a listing of the resource.
     *
     */
    public function index() {
        $usuarios = Usuario::with('persona')->with('roles')->whereHas('roles', function ($query) {
            $query->whereIn('roles.id', [1, 2, 3, 4]);
        })
            ->selectRaw('*,
        (SELECT COUNT(*) FROM usuario_curriculums WHERE usuario_curriculums.usuario_id = usuarios.id) AS tiene_curriculums')
            ->get();
        $roles = Rol::all();
        return Inertia::render('UsuariosEquipo/index', [
            'usuarios' => $usuarios,
            'roles' => $roles,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     */
    public function create() {
        $personas = Usuario::select([
            'usuarios.id',
            'usuarios.nick_name',
            'usuarios.email',
            'personas.id as persona_id',
            'personas.nombre',
            'personas.apellido',
            DB::raw("CONCAT(personas.nombre, ' ', personas.apellido, ' - ', usuarios.email) as fullNombre"),
        ])
            ->join('personas', 'usuarios.persona_id', '=', 'personas.id')
            ->whereHas('roles', function ($query) {$query->where('roles.id', 5);})
            ->whereDoesntHave('roles', function ($query) {$query->whereNotIn('roles.id', [5]);})
            ->get();
        $roles = Rol::all();
        $curriculums = Curriculum::all();
        return Inertia::render('UsuariosEquipo/form', [
            'action' => 'create',
            'roles' => $roles,
            'personas' => $personas,
            'curriculums' => $curriculums,

        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     */
    public function store(Request $request) {

        $id = $request->id;
        $usuario = Usuario::find($id);
        $roles = $request->roles;
        $roles = array_filter($roles, function ($item) {
            return $item['activo'];
        });
        $roles = array_map(function ($item) {
            return $item['id'];
        }, $roles);

        $curriculums = $request->input('curriculums', []);
        if (array_search(RolHelper::$COORDINADOR, $roles)) {
            $curriculums = array_map(function ($item) {
                return $item['id'];
            }, $curriculums);
        }try {
            DB::beginTransaction();
            $state = $usuario->roles()->sync($roles);
            $state = $usuario->curriculums()->sync($curriculums);
            DB::commit();
            if ($state) {
                return response()->json(["message" => "Los Roles fueron asignados exitosamente!"], 200);
            } else {
                return response()->json(["message" => "", 'server' => '¡Los Roles no puedieron ser asignados, intente más tarde!'], 400);
            }
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(["message" => $th->getMessage(), 'server' => '¡Los Roles no puedieron ser asignados, intente más tarde!'], 500);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     */
    public function edit($id) {
        $usuario = Usuario::whereId($id)->with('persona')->with('roles', 'curriculums')->first();
        $roles = Rol::all();
        $curriculums = Curriculum::all();

        return Inertia::render('UsuariosEquipo/form', [
            'action' => 'edit',
            'usuario' => $usuario,
            'roles' => $roles,
            'curriculums' => $curriculums,

        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     */
    public function update(Request $request, $id) {
        $usuario = Usuario::find($id);
        $roles = $request->roles;
        $roles = array_filter($roles, function ($item) {
            return $item['activo'];
        });
        $roles = array_map(function ($item) {
            return $item['id'];
        }, $roles);

        $curriculums = $request->input('curriculums', []);
        if (array_search(RolHelper::$COORDINADOR, $roles)) {
            $curriculums = array_map(function ($item) {
                return $item['id'];
            }, $curriculums);
        }
        try {
            DB::beginTransaction();
            $state = $usuario->roles()->sync($roles);
            $state = $usuario->curriculums()->sync($curriculums);
            DB::commit();
            if ($state) {
                return response()->json(["message" => "Los Roles fueron asignados exitosamente!"], 200);
            } else {
                return response()->json(["message" => "", 'server' => '¡Los Roles no puedieron ser asignados, intente más tarde!'], 400);
            }
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(["message" => $th->getMessage(), 'server' => '¡Los Roles no puedieron ser asignados, intente más tarde!'], 500);
        }

    }

}
