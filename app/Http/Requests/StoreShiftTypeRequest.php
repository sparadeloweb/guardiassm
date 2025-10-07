<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreShiftTypeRequest extends FormRequest
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
            'description' => 'required|string|max:255',
            'value' => 'required|numeric|min:0',
            'patient_value' => 'required|numeric|min:0',
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
            'description.required' => 'La descripción es requerida',
            'value.required' => 'El valor es requerido',
            'value.numeric' => 'El valor debe ser un número',
            'value.min' => 'El valor debe ser mayor a 0',
            'name.string' => 'El nombre debe ser un texto',
            'name.max' => 'El nombre debe tener menos de 255 caracteres',
            'description.string' => 'La descripción debe ser un texto',
            'description.max' => 'La descripción debe tener menos de 255 caracteres',
            'patient_value.required' => 'El valor del paciente es requerido',
            'patient_value.numeric' => 'El valor del paciente debe ser un número',
            'patient_value.min' => 'El valor del paciente debe ser mayor o igual a 0',
        ];
    }
}
