<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class InscripcionRequest extends FormRequest {
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize() {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() {
        $inscripcionId = $this->input('id');
        $rules = [
            'usuario_id' => ['required', 'numeric'],
            'rol_id' => ['required', 'numeric'],
            'grupo_pequeno_id' => ['required', 'numeric'],
            'estado_inscripcion_id' => ['required', 'numeric'],
            'info_adicional' => ['string', 'nullable'],
            // Validación de unicidad compuesta
            'unique_rule' => [
                Rule::unique('inscripciones')
                    ->where('usuario_id', $this->usuario_id)
                    ->where('rol_id', $this->rol_id)
                    ->where('grupo_pequeno_id', $this->grupo_pequeno_id)
                    ->ignore($inscripcionId), // Ignorar en caso de actualización
            ],
        ];

        return $rules;
    }
    public function messages() {
        return [
            'unique_rule' => 'Ya existe una inscripción con estos datos para este usuario.',
        ];
    }
}
