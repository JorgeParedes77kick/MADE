<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreMenuRequest extends FormRequest
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
      $idValidate = $this->route('menu') == NULL ? "": ','.($this->route('menu'). ',id' );
      return [
        'nombre' => 'required|max:50|unique:menus,nombre,'.$idValidate,
        'url_ref' => 'required|max:255',
      ];
    }
}
