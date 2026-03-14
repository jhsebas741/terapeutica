<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class RoutineRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'patient_id' => ['required', 'exists:users,id'],
            'name' => ['required', 'string', 'max:100'],
            'description' => ['nullable', 'string', 'max:500'],
            'steps' => ['required', 'array', 'min:1'],
            'steps.*.pictogram_id' => ['required', 'exists:pictograms,id'],
        ];
    }

    public function attributes(): array
    {
        return [
            'patient_id' => 'paciente',
            'name' => 'nombre',
            'description' => 'descripción',
            'steps' => 'pasos',
            'steps.*.pictogram_id' => 'pictograma del paso',
        ];
    }
}
