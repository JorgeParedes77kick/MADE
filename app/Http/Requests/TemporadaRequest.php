<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TemporadaRequest extends FormRequest {
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize() {
        return isSuperAdmin();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() {
        $rules = [
            'nombre' => 'required:alpha_num',
            'prefijo' => 'required:alpha_num|unique:temporadas,prefijo',
            'fecha_inicio_w' => 'required',
            'fecha_cierre_w' => 'required',
            // 'fecha_inicio' => 'required|date',
            // 'fecha_cierre' => 'required_with:fecha_inicio|date|after:fecha_inicio',
            // "inscripcion_inicio" => "required|date",
            // "inscripcion_cierre" => "nullable|date|after:inscripcion_inicio",
        ];

        if (in_array($this->method(), ['PUT', 'PATCH'])) {
            $rules['id'] = 'required:numeric';
            $rules['prefijo'] = 'required:alpha_num|unique:temporadas,prefijo,' . $this->id;
        }
        return $rules;
    }
}
