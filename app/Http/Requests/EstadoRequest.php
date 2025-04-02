<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EstadoRequest extends FormRequest {
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
        $table = "";
        if ($this->is('estados-asistencia*')) {
            $table = 'estados_asistencias';
        } else if ($this->is('estados-inscripcion*')) {
            $table = 'estados_inscripciones';

        }
        $rules = [
            // 'estado' => 'required:string|unique:temporadas,estado',
            'estado' => 'required:string|unique:' . $table . ',estado',

        ];
        // Debug::info($this->is('estados-asistencia*'));
        // Debug::info($this->is('estados-civil*'));
        // Debug::info($this->is('estados-inscripcion*'));
        if (in_array($this->method(), ['PUT', 'PATCH'])) {
            $rules['id'] = 'required:number';
            $rules['estado'] = 'required:string|unique:' . $table . ',estado,' . $this->id;

        }
        return $rules;
    }
}
