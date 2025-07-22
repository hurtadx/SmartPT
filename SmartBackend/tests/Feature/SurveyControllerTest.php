<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\SurveyResponse;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class SurveyControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected function setUp(): void
    {
        parent::setUp();
    }

    /** @test */
    public function unauthenticated_user_cannot_access_survey_endpoints()
    {
        // Test que usuario no autenticado no puede acceder a las encuestas
        $response = $this->getJson('/api/survey/questions');
        $response->assertStatus(401);

        $response = $this->getJson('/api/survey/status');
        $response->assertStatus(401);

        $response = $this->getJson('/api/survey/results');
        $response->assertStatus(401);

        $response = $this->postJson('/api/survey/submit');
        $response->assertStatus(401);
    }

    /** @test */
    public function authenticated_user_can_get_survey_questions()
    {
        // Crear usuario autenticado
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        // Hacer request a obtener preguntas
        $response = $this->getJson('/api/survey/questions');

        // Verificar respuesta
        $response->assertStatus(200)
                ->assertJsonStructure([
                    'success',
                    'message',
                    'data' => [
                        'questions' => [
                            '*' => [
                                'id',
                                'type',
                                'question',
                                'field_name',
                                'required'
                            ]
                        ],
                        'total_questions'
                    ]
                ]);

        $responseData = $response->json();
        $this->assertTrue($responseData['success']);
        $this->assertCount(5, $responseData['data']['questions']);
        $this->assertEquals(5, $responseData['data']['total_questions']);
    }

    /** @test */
    public function authenticated_user_can_check_survey_status_when_not_completed()
    {
        // Crear usuario que no ha completado encuesta
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $response = $this->getJson('/api/survey/status');

        $response->assertStatus(200)
                ->assertJson([
                    'success' => true,
                    'data' => [
                        'has_completed_survey' => false,
                        'completed_at' => null,
                        'can_take_survey' => true,
                    ]
                ]);
    }

    /** @test */
    public function authenticated_user_can_check_survey_status_when_completed()
    {
        // Crear usuario con encuesta completada
        $user = User::factory()->create();
        $surveyResponse = SurveyResponse::factory()->create([
            'user_id' => $user->id,
            'completed_at' => now()
        ]);
        Sanctum::actingAs($user);

        $response = $this->getJson('/api/survey/status');

        $response->assertStatus(200)
                ->assertJson([
                    'success' => true,
                    'data' => [
                        'has_completed_survey' => true,
                        'can_take_survey' => false,
                    ]
                ]);

        $this->assertNotNull($response->json('data.completed_at'));
    }

    /** @test */
    public function authenticated_user_can_submit_survey_with_valid_data()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $surveyData = [
            'favorite_framework' => 'Laravel porque es muy potente',
            'experience_level' => 'Senior',
            'programming_languages' => ['PHP', 'JavaScript', 'Python'],
            'teamwork_rating' => 4,
            'agile_experience' => true
        ];

        $response = $this->postJson('/api/survey/submit', $surveyData);

        $response->assertStatus(201)
                ->assertJsonStructure([
                    'success',
                    'message',
                    'data' => [
                        'survey_response' => [
                            'id',
                            'completed_at',
                            'user' => [
                                'name',
                                'email'
                            ]
                        ]
                    ]
                ]);

        // Verificar que se guardó en base de datos
        $this->assertDatabaseHas('survey_responses', [
            'user_id' => $user->id,
            'favorite_framework' => 'Laravel porque es muy potente',
            'experience_level' => 'Senior',
            'teamwork_rating' => 4,
            'agile_experience' => true
        ]);

        $responseData = $response->json();
        $this->assertTrue($responseData['success']);
    }

    /** @test */
    public function user_cannot_submit_survey_twice()
    {
        // Crear usuario con encuesta ya completada
        $user = User::factory()->create();
        SurveyResponse::factory()->create([
            'user_id' => $user->id,
            'completed_at' => now()
        ]);
        Sanctum::actingAs($user);

        $surveyData = [
            'favorite_framework' => 'React porque es genial',
            'experience_level' => 'Mid',
            'programming_languages' => ['JavaScript'],
            'teamwork_rating' => 3,
            'agile_experience' => false
        ];

        $response = $this->postJson('/api/survey/submit', $surveyData);

        $response->assertStatus(409)
                ->assertJson([
                    'success' => false,
                    'message' => 'Ya has completado la encuesta anteriormente.'
                ]);
    }

    /** @test */
    public function survey_submission_requires_all_required_fields()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        // Enviar datos incompletos
        $incompleteData = [
            'favorite_framework' => 'Laravel',
            // Faltan campos requeridos
        ];

        $response = $this->postJson('/api/survey/submit', $incompleteData);

        $response->assertStatus(422); // Unprocessable Entity
    }

    /** @test */
    public function authenticated_user_can_get_survey_results_when_completed()
    {
        $user = User::factory()->create();
        $surveyResponse = SurveyResponse::factory()->create([
            'user_id' => $user->id,
            'favorite_framework' => 'Vue.js es increíble',
            'experience_level' => 'Mid',
            'programming_languages' => ['JavaScript', 'PHP'],
            'teamwork_rating' => 5,
            'agile_experience' => true,
            'completed_at' => now()
        ]);
        Sanctum::actingAs($user);

        $response = $this->getJson('/api/survey/results');

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'success',
                    'message',
                    'data' => [
                        'survey_info' => [
                            'completed_at',
                            'user_name'
                        ],
                        'results' => [
                            '*' => [
                                'question',
                                'answer',
                                'type'
                            ]
                        ]
                    ]
                ]);

        $responseData = $response->json();
        $this->assertTrue($responseData['success']);
        $this->assertCount(5, $responseData['data']['results']);
        $this->assertEquals($user->name, $responseData['data']['survey_info']['user_name']);
    }

    /** @test */
    public function user_cannot_get_results_when_survey_not_completed()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $response = $this->getJson('/api/survey/results');

        $response->assertStatus(404)
                ->assertJson([
                    'success' => false,
                    'message' => 'No has completado la encuesta aún.'
                ]);
    }

    /** @test */
    public function survey_questions_have_correct_structure()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $response = $this->getJson('/api/survey/questions');
        $questions = $response->json('data.questions');

        // Verificar que las preguntas tienen la estructura correcta
        foreach ($questions as $question) {
            $this->assertArrayHasKey('id', $question);
            $this->assertArrayHasKey('type', $question);
            $this->assertArrayHasKey('question', $question);
            $this->assertArrayHasKey('field_name', $question);
            $this->assertArrayHasKey('required', $question);
            $this->assertTrue($question['required']);
        }

        // Verificar tipos específicos
        $questionTypes = collect($questions)->pluck('type')->toArray();
        $this->assertContains('textarea', $questionTypes);
        $this->assertContains('radio', $questionTypes);
        $this->assertContains('checkbox', $questionTypes);
        $this->assertContains('range', $questionTypes);
    }

    /** @test */
    public function survey_submission_validates_experience_level()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $invalidData = [
            'favorite_framework' => 'Laravel',
            'experience_level' => 'Invalid Level', // Valor inválido
            'programming_languages' => ['PHP'],
            'teamwork_rating' => 3,
            'agile_experience' => true
        ];

        $response = $this->postJson('/api/survey/submit', $invalidData);
        $response->assertStatus(422);
    }

    /** @test */
    public function survey_submission_validates_teamwork_rating_range()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $invalidData = [
            'favorite_framework' => 'Laravel',
            'experience_level' => 'Senior',
            'programming_languages' => ['PHP'],
            'teamwork_rating' => 10, // Fuera del rango 1-5
            'agile_experience' => true
        ];

        $response = $this->postJson('/api/survey/submit', $invalidData);
        $response->assertStatus(422);
    }

    /** @test */
    public function survey_results_format_answers_correctly()
    {
        $user = User::factory()->create();
        $surveyResponse = SurveyResponse::factory()->create([
            'user_id' => $user->id,
            'favorite_framework' => 'Laravel',
            'experience_level' => 'Senior',
            'programming_languages' => ['PHP', 'JavaScript'],
            'teamwork_rating' => 4,
            'agile_experience' => true,
            'completed_at' => now()
        ]);
        Sanctum::actingAs($user);

        $response = $this->getJson('/api/survey/results');
        $results = $response->json('data.results');

        // Verificar formato específico de respuestas
        $teamworkResult = collect($results)->firstWhere('type', 'rating');
        $this->assertEquals('4/5', $teamworkResult['answer']);

        $agileResult = collect($results)->firstWhere('type', 'boolean');
        $this->assertEquals('Sí', $agileResult['answer']);
    }
}
