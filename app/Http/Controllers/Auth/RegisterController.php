<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Ciudad;
use App\Models\Country;
use App\Models\Nacionalidad;
use App\Models\Nationality;
use App\Models\Pais;
use App\Models\Usuario;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
     */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    public function showRegistrationForm()
    {
        $nationalities = Nacionalidad::all();
        $countries = Pais::orderBy('name')->with(['Regions' => function ($q) {
            $q->orderBy('name');
        }])->get();
        return view('auth.register')
            ->with('nationalities', $nationalities)
            ->with('countries', $countries);
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => ['required', 'string'],
            'lastname' => ['required', 'string'],
            'dni' => ['required', 'string'],
            'gender' => ['required', 'string'],
            'civil_status' => ['required', 'string'],
            'birthdate' => ['required', 'date'],
            'email' => ['required', 'string', 'email', 'unique:users'],
            'nationality_id' => ['required', 'numeric'],
            'region_id' => ['required', 'numeric'],
            'city' => ['required', 'string'],
            'address' => ['required', 'string'],
            'phone' => ['required', 'string'],
            'password' => ['required', 'string', 'min:8', 'confirmed', 'regex:/^(?=.*[A-Z])(?=.*\d)(?=.*[!@#$%^&*,.\-_]).*$/'],
            'password_confirmation' => ['required'],
        ], [
            'name.required' => 'Nombre es requerido.',
            'lastname.required' => 'Apellido es requerido.',
            'dni.required' => 'DNI es requerido.',
            'gender.required' => 'Género es requerido.',
            'civil_status.required' => 'Estado civil es requerido.',
            'birthdate.required' => 'Fecha de nacimiento es requerida.',
            'email.required' => 'Email es requerido.',
            'email.email' => 'E-mail debe ser en formato válido.',
            'email.unique' => 'El email ya está registrado.',
            'nationality_id.required' => 'Nacionalidad es requerida.',
            'region_id.required' => 'Región es requerida.',
            'city.required' => 'Ciudad es requerida.',
            'address.required' => 'Dirección es requerida.',
            'phone.required' => 'Teléfono es requerido.',
            'password.required' => 'La contraseña es requerida.',
            'password.min' => 'El largo de la contraseña debe ser igual o mayor a 8 caracteres.',
            'password.confirmed' => 'Las contraseñas no coinciden.',
            'password.regex' => 'La contraseña debe cumplir los requisitos de seguridad.',
            'password_confirmation.required' => 'Repetir contraseña es requerido.',
        ]);
    }

    public function register(Request $request)
    {
        $input = $request->all();
        $validator = $this->validator($input);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\Models\Usuario
     */
    protected function create(array $data)
    {
        return Usuario::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);
    }
}
