<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SurveyRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check(); // Solo usuarios autenticados pueden enviar encuestas
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'favorite_framework' => 'required|string|max:1000',
            'experience_level' => 'required|in:Junior,Mid,Senior',
            'programming_languages' => 'required|array|min:1',
            'programming_languages.*' => 'in:JavaScript,PHP,Python,Java',
            'teamwork_rating' => 'required|integer|min:1|max:5',
            'agile_experience' => 'required|boolean',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'favorite_framework.required' => 'Debes responder cuál es tu framework favorito.',
            'favorite_framework.max' => 'La respuesta no puede exceder 1000 caracteres.',
            'experience_level.required' => 'Debes seleccionar tu nivel de experiencia.',
            'experience_level.in' => 'El nivel de experiencia debe ser Junior, Mid o Senior.',
            'programming_languages.required' => 'Debes seleccionar al menos un lenguaje de programación.',
            'programming_languages.min' => 'Debes seleccionar al menos un lenguaje.',
            'programming_languages.*.in' => 'Los lenguajes deben ser: JavaScript, PHP, Python o Java.',
            'teamwork_rating.required' => 'Debes calificar qué tanto te gusta trabajar en equipo.',
            'teamwork_rating.min' => 'La calificación debe ser entre 1 y 5.',
            'teamwork_rating.max' => 'La calificación debe ser entre 1 y 5.',
            'agile_experience.required' => 'Debes responder si has trabajado con metodologías ágiles.',
        ];
    }
}
