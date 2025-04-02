<?php
namespace App\Http\Requests;

use App\Helpers\RolHelper;
use App\Models\Usuario;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class UpdatePersonaRequest extends FormRequest {
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize() {
        $id = $id = base64_decode($this->route('persona'));
        // return response()->json(['message' => 'Usuario sin permisos suficientes para realizar el cambio'], 401);

        return $id === Usuario::auth()->id || RolHelper::isValidRol([RolHelper::$ADMIN, RolHelper::$COORDINADOR]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() {
        $rules = [
            // 'dni',
            'idCrypt'               => ['required', 'string'],
            'email'                 => ['required', 'email'],
            'telefono'              => ['required', 'string'],
            'nombre'                => ['required'],
            'apellido'              => ['required'],
            "tipo_documento_id"     => ['nullable', 'numeric'],
            'fecha_nacimiento'      => ['required', 'date'],
            'genero_id'             => ['required', 'numeric'],
            'region_id'             => ['nullable', 'numeric'],
            "ciudad"                => ['nullable', 'string'],
            'estado_civil_id'       => ['required', 'numeric'],
            'nacionalidad_id'       => ['required', 'numeric'],
            "direccion"             => ['nullable', 'string'],
            "codigo_postal"         => ['nullable', 'string'],
            "dni"                   => ['string'],
            "fotografia"            => ['nullable', 'string'],
            "ocupacion"             => ['nullable', 'string'],
            "informacion_adicional" => ['nullable', 'string'],

            'newContrasena'         => ['nullable', 'string'],
            'repitaContrasena'      => ['nullable', 'string'],
        ];
        return $rules;
    }

    public function withValidator($validator) {
        $validator->after(function ($validator) {
            $newContrasena    = $this->input('newContrasena');
            $repitaContrasena = $this->input('repitaContrasena');

            if ($newContrasena && $newContrasena !== $repitaContrasena) {
                $validator->errors()->add('repitaContrasena', 'La confirmación de la contraseña no coincide con la nueva contraseña.');
            }
        });
    }
}
