<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePatientRequest extends FormRequest
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
        $patientId = $this->route('patient');

        return [
            'name' => 'nullable|string|max:255',
            'DNI' => 'nullable|string|max:255|unique:patients,DNI,' . $patientId,
            'phone' => 'nullable|string|max:255',
            'address' => 'nullable|string|max:255',
            'email' => 'nullable|string|email|max:255',
            'gender' => 'required|string|in:male,female',
            'birth_date' => 'nullable|date',
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
            'name.string' => 'El nombre debe ser un texto',
            'name.max' => 'El nombre debe tener menos de 255 caracteres',
            'DNI.unique' => 'El DNI ya existe',
            'DNI.string' => 'El DNI debe ser un texto',
            'DNI.max' => 'El DNI debe tener menos de 255 caracteres',
            'phone.string' => 'El teléfono debe ser un texto',
            'phone.max' => 'El teléfono debe tener menos de 255 caracteres',
            'address.string' => 'La dirección debe ser un texto',
            'address.max' => 'La dirección debe tener menos de 255 caracteres',
            'email.string' => 'El email debe ser un texto',
            'email.max' => 'El email debe tener menos de 255 caracteres',
            'email.email' => 'El email debe ser una dirección de email válida',
            'gender.required' => 'El género es requerido',
            'gender.string' => 'El género debe ser un texto',
            'gender.in' => 'El género debe ser masculino o femenino',
            'birth_date.date' => 'La fecha de nacimiento debe ser una fecha válida',
        ];
    }
}
