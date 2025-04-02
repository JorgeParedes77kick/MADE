<?php

namespace App\Http\Requests;

use App\Models\Pais;
use Illuminate\Foundation\Http\FormRequest;

class StorePersonaRequest extends FormRequest
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
        $idValidate = $this->route('persona') == NULL ? "": ','.($this->route('persona')->id. ',id' );
        return [
          'nombre' => "required|regex:/^[a-zA-Z\u00C0-\u017F'\-_\s]*$/|max:50",
          'apellido' => "required|regex:/^[a-zA-Z\u00C0-\u017F'\-_\s]*$/|max:50",
          'tipo_documento_id' => 'required|numeric',
          'dni' => 'required|regex:/^[a-zA-Z0-9\s]*$/|max:20|unique:personas,dni,'.$idValidate,
          'fecha_nacimiento' => 'required|date',
          'genero_id' => 'required|numeric',
          'estado_civil_id' => 'required|numeric',
          'region_id' => 'required|numeric',
          'ciudad' => "nullable|regex:/^[a-zA-Z\u00C0-\u017F'\-_\s]*$/|max:100",
          'nacionalidad_id' => 'required|numeric',
          'direccion' => "nullable|regex:/^[a-zA-Z0-9\u00C0-\u017F'\-_\s]*$/|max:250",
          'telefono' => 'nullable|max:20|regex:/\+[0-9\s-]+/',
          'ocupacion' => "nullable|regex:/^[a-zA-Z\u00C0-\u017F'\-_\s]*$/|max:250",
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
        'nombre.required' => 'Debe ingresar un nombre',
        'apellido.required' => 'Debe ingresar un apellido',
      ];
    }
    protected function prepareForValidation()
    {
      $pais = Pais::where('id', $this->pais_residencia)->first();
      if($pais) {
        if ($pais->nombre == 'Chile') {
          $uno = $this->dni;
          $dos = preg_replace('([^A-Za-z0-9])', '', $uno); // quita todo lo que no sea letras o numeros
          $tre = str_pad($dos, 11, "0", STR_PAD_LEFT); // completa con ceros a la izquierda
          $this->merge([
            'dni' => $tre,
          ]);
        }
      }

    }

}
