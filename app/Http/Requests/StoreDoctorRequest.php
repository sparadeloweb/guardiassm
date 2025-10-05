<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreDoctorRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
    */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
    */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'age' => 'required|integer|min:18|max:100',
            'email' => 'required|email|unique:doctors',
            'phone' => 'required|string|max:20',
            'address' => 'required|string|max:255',
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
            'name.required' => 'El nombre es requerido',
            'name.string' => 'El nombre no es válido',
            'name.max' => 'El nombre no puede tener más de 255 caracteres',
            'age.required' => 'La edad es requerida',
            'age.integer' => 'La edad no es válida',
            'age.min' => 'La edad no puede ser menor a 18',
            'age.max' => 'La edad no puede ser mayor a 100',
            'email.required' => 'El email es requerido',
            'email.email' => 'El email no es válido',
            'email.unique' => 'El email ya existe',
            'phone.required' => 'El teléfono es requerido',
            'phone.string' => 'El teléfono no es válido',
            'phone.max' => 'El teléfono no puede tener más de 20 caracteres',
            'address.required' => 'La dirección es requerida',
            'address.string' => 'La dirección no es válida',
            'address.max' => 'La dirección no puede tener más de 255 caracteres',
        ];
    }
}
