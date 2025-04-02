<?php

namespace App\Http\Controllers\Auth;

use App\Helpers\Debug;
use App\Http\Controllers\Controller;
use App\Models\Rol;
use App\Models\Usuario;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class LoginController extends Controller {
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
     */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $_redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct() {
        $this->middleware('guest')->except('logout');
    }

    public function showLoginForm() {
        $roles = Rol::all();
        $lastRole = $roles->pop(); // Extrae el último elemento de la colección
        $roles->prepend($lastRole); // Inserta el último elemento al principio de la colección

        //return view('auth.login')->with('roles', $roles);
        return Inertia::render('Login/LoginPage', [
            'roles' => $roles,
        ]);
    }
    public function login(Request $request) {
        $input = $request->all();
        Debug::info($input);
        // $validator = Validator::make($input, [
        //     'email' => 'required|email',
        //     'password' => 'required',
        // ]);
        Debug::info($input);
        // if ($validator->fails()) {
        //     return response()->json(['message' => $validator->errors()], 422);
        // }
        $user = Usuario::whereEmail($request->email)->first();
        if (!($user && $user->roles()->where('rol_id', $request->role)->exists())) {
        }
        $credentials = $request->only('email', 'password');
        if ($this->guard($request)->attempt($credentials)) {
            $request->session()->regenerate();
            $this->clearLoginAttempts($request);
            $response = $this->authenticated($request, $this->guard($request)->user());
            return response()->json(["user" => $response]);
        } else {
            return response()->json(["message" => "Credenciales inválidas"], 403);
        }
    }
    protected function guard(Request $request) {
        $role = Rol::whereId($request->role_id)->first();
        if ($role) {
            return Auth::guard($role->name);
        }
        return Auth::guard();
    }
}
