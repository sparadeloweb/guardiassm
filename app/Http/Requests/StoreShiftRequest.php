<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreShiftRequest extends FormRequest
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
            'doctor_id' => 'required|exists:doctors,id',
            'shift_type_id' => 'required|exists:shift_types,id',
            'starts_at' => 'required|date',
            'ends_at' => 'required|date|after:starts_at',
            'notes' => 'nullable|string|max:255',
            'attentions' => 'nullable|array',
            'attentions.*.patient_id' => 'required|exists:patients,id',
                'attentions.*.attended_at' => 'required|date|after_or_equal:starts_at|before_or_equal:ends_at',
            'attentions.*.notes' => 'nullable|string|max:255',
            'attentions.*.pathologies' => 'nullable|array',
            'attentions.*.pathologies.*' => 'required|exists:pathologies,id',
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
            'doctor_id.required' => 'El doctor es requerido',
            'doctor_id.exists' => 'El doctor no existe',
            'shift_type_id.required' => 'El tipo de turno es requerido',
            'shift_type_id.exists' => 'El tipo de turno no existe',
            'starts_at.required' => 'La fecha de inicio es requerida',
            'starts_at.date' => 'La fecha de inicio debe ser una fecha válida',
            'ends_at.required' => 'La fecha de fin es requerida',
            'ends_at.date' => 'La fecha de fin debe ser una fecha válida',
            'ends_at.after' => 'La fecha de fin debe ser posterior a la fecha de inicio',
            'notes.string' => 'Las notas deben ser un texto',
            'notes.max' => 'Las notas deben tener menos de 255 caracteres',
            'attentions.array' => 'Las atenciones deben ser un array',
            'attentions.*.patient_id.required' => 'El paciente es requerido',
            'attentions.*.patient_id.exists' => 'El paciente no existe',
                'attentions.*.attended_at.required' => 'La fecha de atención es requerida',
                'attentions.*.attended_at.date' => 'La fecha de atención debe ser una fecha válida',
                'attentions.*.attended_at.after_or_equal' => 'La fecha de atención debe ser posterior o igual a la fecha de inicio de la guardia',
                'attentions.*.attended_at.before_or_equal' => 'La fecha de atención debe ser anterior o igual a la fecha de fin de la guardia',
            'attentions.*.notes.string' => 'Las notas deben ser un texto',
            'attentions.*.notes.max' => 'Las notas deben tener menos de 255 caracteres',
            'attentions.*.pathologies.array' => 'Las patologías deben ser un array',
            'attentions.*.pathologies.*.required' => 'La patología es requerida',
            'attentions.*.pathologies.*.exists' => 'La patología no existe',
        ];
    }
}
