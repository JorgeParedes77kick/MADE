<?php

namespace App\Actions\Fortify;

use App\Models\Usuario;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Laravel\Fortify\Contracts\CreatesNewUsers;

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules;

    /**
     * Validate and create a newly registered user.
     *
     * @param  array<string, string>  $input
     */
    public function create(array $input): ?Usuario
    {
        $validator = Validator::make($input, [
            'nick_name' => 'required|max:100|regex:/^[a-z]+\.[a-z]+$/',
            'email' => 'required|email|max:255|'.Rule::unique(Usuario::class),
            'password' => 'required|regex:/^[a-zA-Z0-9_\-\.\*]{5,50}$/',
            'persona_id' => 'required|numeric',
        ]);

        if($validator->fails()){
          throw ValidationException::withMessages($validator->getMessageBag()->getMessages());
        }

        $validator->validated();

        return Usuario::create([
            'nick_name' => $input['nick_name'],
            'email' => $input['email'],
            'password' => Hash::make($input['password']),
            'persona_id' => $input['persona_id'],
        ]);
    }
}
