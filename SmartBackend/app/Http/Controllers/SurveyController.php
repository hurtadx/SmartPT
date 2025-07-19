<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\SurveyRequest;
use App\Models\SurveyResponse;
use Illuminate\Http\Request;

class SurveyController extends Controller
{
    /**
     * Constructor - Todas las rutas necesitan autenticación
     */
    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }

    /**
     * Obtener las preguntas de la encuesta
     */
    public function getQuestions()
    {
        try {
            $questions = [
                [
                    'id' => 1,
                    'type' => 'textarea',
                    'question' => '¿Cuál es tu framework favorito y por qué?',
                    'field_name' => 'favorite_framework',
                    'required' => true,
                    'max_length' => 1000
                ],
                [
                    'id' => 2,
                    'type' => 'radio',
                    'question' => '¿Cuál es tu nivel de experiencia en React?',
                    'field_name' => 'experience_level',
                    'required' => true,
                    'options' => ['Junior', 'Mid', 'Senior']
                ],
                [
                    'id' => 3,
                    'type' => 'checkbox',
                    'question' => '¿Qué lenguajes de programación conoces?',
                    'field_name' => 'programming_languages',
                    'required' => true,
                    'options' => ['JavaScript', 'PHP', 'Python', 'Java']
                ],
                [
                    'id' => 4,
                    'type' => 'range',
                    'question' => 'En una escala del 1 al 5, ¿qué tanto te gusta trabajar en equipo?',
                    'field_name' => 'teamwork_rating',
                    'required' => true,
                    'min' => 1,
                    'max' => 5
                ],
                [
                    'id' => 5,
                    'type' => 'radio',
                    'question' => '¿Has trabajado con metodologías ágiles?',
                    'field_name' => 'agile_experience',
                    'required' => true,
                    'options' => [
                        ['value' => true, 'label' => 'Sí'],
                        ['value' => false, 'label' => 'No']
                    ]
                ]
            ];

            return response()->json([
                'success' => true,
                'message' => 'Preguntas obtenidas exitosamente',
                'data' => [
                    'questions' => $questions,
                    'total_questions' => count($questions)
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener las preguntas: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Guardar las respuestas de la encuesta
     */
    public function submitSurvey(SurveyRequest $request)
    {
        try {
            $user = $request->user();

            // Verificar si el usuario ya completó la encuesta
            if ($user->hasCompletedSurvey()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Ya has completado la encuesta anteriormente.',
                ], 409); // 409 = Conflict
            }

            // Crear la respuesta de la encuesta
            $surveyResponse = SurveyResponse::create([
                'user_id' => $user->id,
                'favorite_framework' => $request->favorite_framework,
                'experience_level' => $request->experience_level,
                'programming_languages' => $request->programming_languages,
                'teamwork_rating' => $request->teamwork_rating,
                'agile_experience' => $request->agile_experience,
                'completed_at' => now(),
            ]);

            return response()->json([
                'success' => true,
                'message' => '¡Encuesta enviada exitosamente!',
                'data' => [
                    'survey_response' => [
                        'id' => $surveyResponse->id,
                        'completed_at' => $surveyResponse->completed_at,
                        'user' => [
                            'name' => $user->name,
                            'email' => $user->email,
                        ]
                    ]
                ]
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al guardar la encuesta: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Obtener los resultados de la encuesta del usuario
     */
    public function getResults(Request $request)
    {
        try {
            $user = $request->user();
            $surveyResponse = $user->latestSurveyResponse();

            if (!$surveyResponse) {
                return response()->json([
                    'success' => false,
                    'message' => 'No has completado la encuesta aún.',
                ], 404);
            }

            // Formatear las respuestas para mostrar de forma bonita
            $formattedResults = [
                [
                    'question' => '¿Cuál es tu framework favorito y por qué?',
                    'answer' => $surveyResponse->favorite_framework,
                    'type' => 'text'
                ],
                [
                    'question' => '¿Cuál es tu nivel de experiencia en React?',
                    'answer' => $surveyResponse->experience_level,
                    'type' => 'selection'
                ],
                [
                    'question' => '¿Qué lenguajes de programación conoces?',
                    'answer' => $surveyResponse->programming_languages,
                    'type' => 'multiple'
                ],
                [
                    'question' => 'En una escala del 1 al 5, ¿qué tanto te gusta trabajar en equipo?',
                    'answer' => $surveyResponse->teamwork_rating . '/5',
                    'type' => 'rating'
                ],
                [
                    'question' => '¿Has trabajado con metodologías ágiles?',
                    'answer' => $surveyResponse->agile_experience ? 'Sí' : 'No',
                    'type' => 'boolean'
                ]
            ];

            return response()->json([
                'success' => true,
                'message' => 'Resultados obtenidos exitosamente',
                'data' => [
                    'survey_info' => [
                        'completed_at' => $surveyResponse->completed_at,
                        'user_name' => $user->name,
                    ],
                    'results' => $formattedResults
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener los resultados: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Verificar si el usuario ya completó la encuesta
     */
    public function checkStatus(Request $request)
    {
        try {
            $user = $request->user();
            $hasCompleted = $user->hasCompletedSurvey();
            $surveyResponse = $user->latestSurveyResponse();

            return response()->json([
                'success' => true,
                'data' => [
                    'has_completed_survey' => $hasCompleted,
                    'completed_at' => $hasCompleted ? $surveyResponse->completed_at : null,
                    'can_take_survey' => !$hasCompleted,
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al verificar el estado: ' . $e->getMessage(),
            ], 500);
        }
    }
}
