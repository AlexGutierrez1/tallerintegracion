<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UserRequest extends FormRequest
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
        return [
            'name' => [
                'required',
                'min:5'
            ],
            'username' => [
                'required',
                'min:5',
                Rule::when(
                    request()->isMethod('POST'),
                    Rule::unique('App\Models\User','username')
                ),
                Rule::when(
                    request()->isMethod('PUT'),
                    Rule::unique('App\Models\User','username')->ignore($this->user)
                ),
            ],
            'rut' => [
                'required',
                'cl_rut',
                'min:5',
                Rule::when(
                    request()->isMethod('POST'),
                    Rule::unique('App\Models\User','rut')
                ),
                Rule::when(
                    request()->isMethod('PUT'),
                    Rule::unique('App\Models\User','rut')->ignore($this->user)
                ),
            ],
            'email' => [
                'required',
                'email',
                Rule::when(
                    request()->isMethod('POST'),
                    Rule::unique('App\Models\User','email')
                ),
                Rule::when(
                    request()->isMethod('PUT'),
                    Rule::unique('App\Models\User','email')->ignore($this->user)
                ),
            ],
            'password' => [
                Rule::when(
                    request()->isMethod('POST'),
                    'required'
                ),
                Rule::when(
                    request()->isMethod('PUT'),
                    'sometimes'
                ),
                'min:5'
            ]
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'Inserta un nombre',
            'name.min'=>'Mínimo 5 caracteres',
            'username.required' => 'Inserta un nombre de usuario',
            'username.min'=>'Mínimo 5 caracteres',
            'username.unique'=>'Nombre de usuario ya existe',
            'rut.required' => 'Inserta un rut',
            'rut.cl_rut'=>'RUT inválido',
            'rut.min'=>'Mínimo 5 caracteres',
            'rut.unique'=>'RUT ya existe',
            'email.required' => 'Inserta un correo',
            'email.unique' => 'Correo ya existe',
            'password.required' => 'Ingresa una contraseña',
            'password.min' => 'Mínimo 5 caracteres'
        ];
    }
}
