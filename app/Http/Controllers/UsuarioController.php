<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUsuarioRequest;
use App\Http\Requests\UpdateUsuarioRequest;
use App\Models\PasswordResets;
use App\Models\Usuario;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UsuarioController extends Controller {
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index() {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create() {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreUsuarioRequest $request
     * @return Response
     */
    public function store(StoreUsuarioRequest $request) {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param Usuario $usuario
     * @return Response
     */
    public function show(Usuario $usuario) {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param Usuario $usuario
     * @return Response
     */
    public function edit(Usuario $usuario) {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateUsuarioRequest $request
     * @param Usuario $usuario
     * @return Response
     */
    public function update(UpdateUsuarioRequest $request, Usuario $usuario) {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Usuario $usuario
     * @return Response
     */
    public function destroy(Usuario $usuario) {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param string $email
     * @param string $token
     * @return JsonResponse
     */
    public function canResetPass(string $email, string $token) {
        $reset = PasswordResets::where('email', $email)->first();
        if ($reset) {
            if ((!Carbon::parse($reset->created_at)->addSeconds(60 * 60)->isPast() && Hash::check($token, $reset->token))) {
                return response()->json(['canResetPass' => true], 200);
            }
        }
        return response()->json(['canResetPass' => false], 200);
    }

    public function userRoles() {
        $user = auth()->user();
        $roles = $user->roles()->get();
        if ($roles) {
            return response($roles, 200);
        } else {
            return response($roles, 400);
        }

    }
}
