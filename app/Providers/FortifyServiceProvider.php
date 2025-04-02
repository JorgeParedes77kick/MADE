<?php

namespace App\Providers;

use App\Actions\Fortify\CreateNewUser;
use App\Actions\Fortify\ResetUserPassword;
use App\Actions\Fortify\UpdateUserPassword;
use App\Actions\Fortify\UpdateUserProfileInformation;
use App\Helpers\Debug;
use App\Models\EstadoCivil;
use App\Models\Genero;
use App\Models\Nacionalidad;
use App\Models\Pais;
use App\Models\TipoDocumento;
use App\Models\Usuario;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Laravel\Fortify\Fortify;

class FortifyServiceProvider extends ServiceProvider {
    /**
     * Register any application services.
     */
    public function register(): void {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void {
        Fortify::createUsersUsing(CreateNewUser::class);
        Fortify::updateUserProfileInformationUsing(UpdateUserProfileInformation::class);
        Fortify::updateUserPasswordsUsing(UpdateUserPassword::class);
        Fortify::resetUserPasswordsUsing(ResetUserPassword::class);

        RateLimiter::for('login', function (Request $request) {
            $throttleKey = Str::transliterate(Str::lower($request->input(Fortify::username())) . '|' . $request->ip());

            return Limit::perMinute(5)->by($throttleKey);
        });

        RateLimiter::for('two-factor', function (Request $request) {
            return Limit::perMinute(5)->by($request->session()->get('login.id'));
        });

        Fortify::loginView(function () {
            return Inertia::render('Login/LoginPage');
        });

        Fortify::registerView(function () {

            $tipoDocumentos = TipoDocumento::select('id', 'nombre')->orderBy('nombre', 'asc')->get();
            $generos = Genero::select('id', 'nombre')->orderBy('nombre', 'asc')->get();
            $estadosCiviles = EstadoCivil::select('id', 'estado')->orderBy('estado', 'asc')->get();
            $nacionalidades = Nacionalidad::select('id', 'nombre')->orderBy('nombre', 'asc')->get();
            $paises = Pais::select('id', 'nombre')->with('regiones')->orderBy('nombre', 'asc')->get();

            return Inertia::render('Register/RegisterPage',
                [
                    'genderList' => $generos,
                    'civilStatusList' => $estadosCiviles,
                    'nationalityList' => $nacionalidades,
                    'countryList' => $paises,
                    'typeDocumentsList' => $tipoDocumentos,
                ]);

        });

        Fortify::authenticateUsing(function (Request $request) {
            $user = Usuario::where('email', $request->email)->first();
            if ($user && Hash::check($request->password, $user->password)) {
                $rol = $user->roles()->first();
                if ($rol) {
                    $request->session()->put('rol_id', $rol->id);
                    return $user;
                } else {
                    Debug::info("Usuario sin roles, agregue rol");
                    return false;
                }
            } else {
                Debug::info("Usuario no encontrado");
                return false;
            }
        });

        Fortify::requestPasswordResetLinkView(function () {
            return Inertia::render('Login/ForgotPassPage');
        });

        Fortify::resetPasswordView(function ($request) {
            $mail = $request->query('email');
            return Inertia::render('Login/ResetPasswordPage', ['mail' => $mail]);
        });

    }
}
