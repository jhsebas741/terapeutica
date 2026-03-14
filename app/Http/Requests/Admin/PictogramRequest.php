<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class PictogramRequest extends FormRequest
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
        $imageRequired = $this->isMethod('POST') && ! $this->filled('icon_text');
        $iconTextRequired = $this->isMethod('POST') && ! $this->hasFile('image');

        return [
            'category_id' => ['required', 'exists:categories,id'],
            'name' => ['required', 'string', 'max:100'],
            'description' => ['nullable', 'string', 'max:500'],
            'image' => [$imageRequired ? 'required_without:icon_text' : 'nullable', 'image', 'mimes:jpg,jpeg,png,webp,svg', 'max:2048'],
            'icon_text' => [$iconTextRequired ? 'required_without:image' : 'nullable', 'string', 'max:10'],
            'audio' => ['nullable', 'file', 'mimes:mp3,wav,ogg', 'max:5120'],
            'difficulty_level' => ['required', 'integer', 'min:1', 'max:5'],
            'is_active' => ['boolean'],
        ];
    }

    public function attributes(): array
    {
        return [
            'category_id' => 'categoría',
            'name' => 'nombre',
            'description' => 'descripción',
            'image' => 'imagen',
            'icon_text' => 'icono/emoji',
            'audio' => 'audio',
            'difficulty_level' => 'nivel de dificultad',
            'is_active' => 'estado activo',
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'image.required_without' => 'Debes proporcionar una imagen o un emoji.',
            'icon_text.required_without' => 'Debes proporcionar un emoji o una imagen.',
        ];
    }
}
