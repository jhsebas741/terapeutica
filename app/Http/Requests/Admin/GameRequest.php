<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class GameRequest extends FormRequest
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
        $rules = [
            'name' => ['required', 'string', 'max:100'],
            'description' => ['nullable', 'string', 'max:500'],
            'type' => ['required', Rule::in(['emparejar', 'secuencia', 'emocion'])],
            'level' => ['required', 'integer', 'min:1', 'max:10'],
            'elements' => ['required', 'array', 'min:2'],
            'elements.*.pictogram_id' => ['required', 'exists:pictograms,id'],
        ];

        if ($this->input('type') === 'emparejar') {
            $rules['elements'] = ['required', 'array', 'size:4'];
        }

        if ($this->input('type') === 'secuencia') {
            $rules['elements.*.sequence_order'] = ['required', 'integer', 'min:1'];
        }

        if ($this->input('type') === 'emocion') {
            $rules['description'] = ['required', 'string', 'max:500'];
            $rules['elements'] = ['required', 'array', 'size:4'];
            $rules['elements.*.sequence_order'] = ['required', 'integer', 'min:1'];
        }

        return $rules;
    }

    public function attributes(): array
    {
        return [
            'name' => 'nombre',
            'description' => 'descripción',
            'type' => 'tipo',
            'level' => 'nivel',
            'elements' => 'elementos',
            'elements.*.pictogram_id' => 'pictograma del elemento',
            'elements.*.sequence_order' => 'orden del elemento',
            'elements.*.situation_text' => 'texto de la situación',
        ];
    }
}
