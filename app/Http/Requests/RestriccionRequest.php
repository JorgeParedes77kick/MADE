<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RestriccionRequest extends FormRequest {
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
            'curriculum_id' => ['required', 'numeric'],
            'tipo_restriccion_id' => ['required', 'numeric'],
        ];

        $uniqueRule = Rule::unique('restricciones', 'tipo_restriccion_id')
            ->where(function ($query) {
                $query->where('curriculum_id', $this->curriculum_id);
            });

        if (in_array($this->method(), ['PUT', 'PATCH'])) {
            $rules['id'] = 'required|numeric';
            $rules['tipo_restriccion_id'][] = $uniqueRule->ignore($this->id);
        } else {
            $rules['tipo_restriccion_id'][] = $uniqueRule;
        }

        return $rules;
    }

}
