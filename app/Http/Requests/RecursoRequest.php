<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RecursoRequest extends FormRequest {
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
            'nombre' => ['required', 'string'],
            'link_lectura' => ['required', 'string'],
            'link_escritura' => ['required', 'string'],
            'clase' => ['required', 'string'],
            'ciclo_id' => ['required', 'numeric'],

        ];

        if (in_array($this->method(), ['PUT', 'PATCH'])) {
            $rules['id'] = 'required|numeric';
        }

        return $rules;
    }

}
