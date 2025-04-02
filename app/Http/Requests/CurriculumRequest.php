<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CurriculumRequest extends FormRequest {
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
        $rules = [
            // 'nombre' => 'required|string|max:80|unique:curriculums,nombre',
            'nombre' => 'required|string|max:80',
            'libro' => 'nullable|string|max:80',
            'descripcion' => 'nullable|string',
            'cantidad_clases' => 'required|integer',
            'cantidad_cupos' => 'required|integer',
            'imagen' => 'required_without:imagenFile',
            'imagenFile' => 'required_without:imagen|file',
            // 'activo' => 'required',

        ];
        // if (in_array($this->method(), ['PUT', 'PATCH'])) {
        //     $rules['id'] = 'required:numeric';
        //     $rules['nombre'] = 'required|string|max:80|unique:curriculums,nombre,' . $this->id;
        // }
        return $rules;
    }
}
