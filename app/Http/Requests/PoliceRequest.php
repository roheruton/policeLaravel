<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PoliceRequest extends FormRequest
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
            'ci' => 'required',
            'name' => 'required',
            'last_name' => 'required',
            'dateOfBirth' => 'required',
            'photos.*' => 'required|mimes:png,jpg|image',
            'photos' => 'max:3|min:2',
        ];
    }

    public function messages()
    {
        return [
            'ci.required' => 'El campo ci es requerido',
            'name.required' => 'El campo nombre es requerido',
            'last_name.required' => 'El campo apellido es requerido',
            'dateOfBirth' => 'El campo fecha de nacimiento es requerido',
            'photos.*.required' => 'El campo de fotos es requerido',
            'photos.max' => 'Solo puede subir 3 imágenes',
            'photos.min' => 'Necesita subir por lo menos 2 imágenes',
        ];
    }
}
