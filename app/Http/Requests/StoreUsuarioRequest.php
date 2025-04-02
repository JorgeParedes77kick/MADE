<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreUsuarioRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
      $idValidate = $this->route('usuarios') == NULL ? "": ','.($this->route('usuarios')->id. ',id' );
      return [
        'email' => 'required|email|max:255|unique:usuarios,email,'.$idValidate,
        'password' => 'required|regex:/^[a-zA-Z0-9_\-\.\*]{5,50}$/|max:255',
        'nick_name' => 'required|max:100|regex:/^[a-z]+\.[a-z]+$/',
        'persona_id' => 'required|numeric',
      ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
      return [
        'nick_name' => 'Nick Name no ingresado',
        'email' => 'Email no ingresado',
        'password' => 'Password no ingresado',
        'persona_id' => 'Error al asociar Persona',
      ];
    }
}
