<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateShiftRequest extends FormRequest
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
            'doctor_id.exists' => 'El doctor seleccionado no existe',
            'shift_type_id.required' => 'El tipo de guardia es requerido',
            'shift_type_id.exists' => 'El tipo de guardia seleccionado no existe',
            'starts_at.required' => 'La fecha y hora de inicio es requerida',
            'starts_at.date' => 'La fecha y hora de inicio debe ser una fecha válida',
            'ends_at.required' => 'La fecha y hora de fin es requerida',
            'ends_at.date' => 'La fecha y hora de fin debe ser una fecha válida',
            'ends_at.after' => 'La fecha de fin debe ser posterior a la fecha de inicio',
            'notes.string' => 'Las notas deben ser un texto',
            'notes.max' => 'Las notas no pueden exceder los 255 caracteres',
            'attentions.array' => 'Las atenciones deben ser una lista válida',
            'attentions.*.patient_id.required' => 'El paciente es requerido para cada atención',
            'attentions.*.patient_id.exists' => 'El paciente seleccionado no existe',
            'attentions.*.attended_at.required' => 'El horario de atención es requerido',
            'attentions.*.attended_at.date' => 'El horario de atención debe ser una fecha válida',
            'attentions.*.attended_at.after_or_equal' => 'El horario de atención debe ser posterior o igual al inicio de la guardia',
            'attentions.*.attended_at.before_or_equal' => 'El horario de atención debe ser anterior o igual al fin de la guardia',
            'attentions.*.notes.string' => 'Las notas deben ser un texto',
            'attentions.*.notes.max' => 'Las notas no pueden exceder los 255 caracteres',
            'attentions.*.pathologies.array' => 'Las patologías deben ser una lista válida',
            'attentions.*.pathologies.*.required' => 'La patología es requerida',
            'attentions.*.pathologies.*.exists' => 'La patología seleccionada no existe',
        ];
    }
}
