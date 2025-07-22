<?php

namespace Tests\Unit;

use App\Models\User;
use App\Models\SurveyResponse;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class UserModelTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function user_has_correct_fillable_attributes()
    {
        $fillableAttributes = ['name', 'email', 'password'];
        $user = new User();
        
        $this->assertEquals($fillableAttributes, $user->getFillable());
    }

    /** @test */
    public function user_hides_sensitive_attributes()
    {
        $hiddenAttributes = ['password', 'remember_token'];
        $user = new User();
        
        $this->assertEquals($hiddenAttributes, $user->getHidden());
    }

    /** @test */
    public function user_password_is_automatically_hashed()
    {
        $user = User::factory()->create([
            'password' => 'plain_password'
        ]);

        $this->assertNotEquals('plain_password', $user->password);
        $this->assertTrue(Hash::check('plain_password', $user->password));
    }

    /** @test */
    public function user_can_have_multiple_survey_responses()
    {
        $user = User::factory()->create();
        
        // Crear múltiples respuestas de encuesta
        $response1 = SurveyResponse::factory()->create(['user_id' => $user->id]);
        $response2 = SurveyResponse::factory()->create(['user_id' => $user->id]);
        $response3 = SurveyResponse::factory()->create(['user_id' => $user->id]);

        $this->assertCount(3, $user->surveyResponses);
        $this->assertTrue($user->surveyResponses->contains($response1));
        $this->assertTrue($user->surveyResponses->contains($response2));
        $this->assertTrue($user->surveyResponses->contains($response3));
    }

    /** @test */
    public function has_completed_survey_returns_false_when_no_survey_responses()
    {
        $user = User::factory()->create();

        $this->assertFalse($user->hasCompletedSurvey());
    }

    /** @test */
    public function has_completed_survey_returns_false_when_survey_not_completed()
    {
        $user = User::factory()->create();
        
        // Crear respuesta sin completed_at
        SurveyResponse::factory()->create([
            'user_id' => $user->id,
            'completed_at' => null
        ]);

        $this->assertFalse($user->hasCompletedSurvey());
    }

    /** @test */
    public function has_completed_survey_returns_true_when_survey_completed()
    {
        $user = User::factory()->create();
        
        // Crear respuesta con completed_at
        SurveyResponse::factory()->create([
            'user_id' => $user->id,
            'completed_at' => now()
        ]);

        $this->assertTrue($user->hasCompletedSurvey());
    }

    /** @test */
    public function has_completed_survey_returns_true_with_multiple_completed_surveys()
    {
        $user = User::factory()->create();
        
        // Crear múltiples respuestas completadas
        SurveyResponse::factory()->create([
            'user_id' => $user->id,
            'completed_at' => now()->subDays(2)
        ]);
        SurveyResponse::factory()->create([
            'user_id' => $user->id,
            'completed_at' => now()->subDay()
        ]);

        $this->assertTrue($user->hasCompletedSurvey());
    }

    /** @test */
    public function latest_survey_response_returns_null_when_no_responses()
    {
        $user = User::factory()->create();

        $this->assertNull($user->latestSurveyResponse());
    }

    /** @test */
    public function latest_survey_response_returns_most_recent_response()
    {
        $user = User::factory()->create();
        
        // Crear respuestas en diferentes fechas
        $oldResponse = SurveyResponse::factory()->create([
            'user_id' => $user->id,
            'created_at' => now()->subDays(5)
        ]);
        $middleResponse = SurveyResponse::factory()->create([
            'user_id' => $user->id,
            'created_at' => now()->subDays(2)
        ]);
        $newestResponse = SurveyResponse::factory()->create([
            'user_id' => $user->id,
            'created_at' => now()
        ]);

        $latestResponse = $user->latestSurveyResponse();
        
        $this->assertNotNull($latestResponse);
        $this->assertEquals($newestResponse->id, $latestResponse->id);
        $this->assertNotEquals($oldResponse->id, $latestResponse->id);
        $this->assertNotEquals($middleResponse->id, $latestResponse->id);
    }

    /** @test */
    public function user_can_be_created_with_factory()
    {
        $user = User::factory()->create([
            'name' => 'André Hurtado',
            'email' => 'andre@example.com'
        ]);

        $this->assertDatabaseHas('users', [
            'name' => 'André Hurtado',
            'email' => 'andre@example.com'
        ]);

        $this->assertEquals('André Hurtado', $user->name);
        $this->assertEquals('andre@example.com', $user->email);
    }

    /** @test */
    public function user_factory_creates_unique_emails()
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        $user3 = User::factory()->create();

        $this->assertNotEquals($user1->email, $user2->email);
        $this->assertNotEquals($user2->email, $user3->email);
        $this->assertNotEquals($user1->email, $user3->email);
    }

    /** @test */
    public function user_relationship_with_survey_responses_works_correctly()
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        
        // Crear respuestas para el usuario
        $userResponse1 = SurveyResponse::factory()->create(['user_id' => $user->id]);
        $userResponse2 = SurveyResponse::factory()->create(['user_id' => $user->id]);
        
        // Crear respuesta para otro usuario
        $otherUserResponse = SurveyResponse::factory()->create(['user_id' => $otherUser->id]);

        // Verificar que el usuario solo tiene sus propias respuestas
        $this->assertCount(2, $user->surveyResponses);
        $this->assertTrue($user->surveyResponses->contains($userResponse1));
        $this->assertTrue($user->surveyResponses->contains($userResponse2));
        $this->assertFalse($user->surveyResponses->contains($otherUserResponse));
    }

    /** @test */
    public function user_survey_completion_status_is_independent_per_user()
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        
        // Solo user1 completa encuesta
        SurveyResponse::factory()->create([
            'user_id' => $user1->id,
            'completed_at' => now()
        ]);

        $this->assertTrue($user1->hasCompletedSurvey());
        $this->assertFalse($user2->hasCompletedSurvey());
    }

    /** @test */
    public function user_latest_survey_response_is_scoped_to_user()
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        
        // Crear respuestas para ambos usuarios
        $user1Response = SurveyResponse::factory()->create([
            'user_id' => $user1->id,
            'created_at' => now()->subHour()
        ]);
        $user2Response = SurveyResponse::factory()->create([
            'user_id' => $user2->id,
            'created_at' => now() // Más reciente que user1
        ]);

        // Verificar que cada usuario obtiene su propia respuesta más reciente
        $this->assertEquals($user1Response->id, $user1->latestSurveyResponse()->id);
        $this->assertEquals($user2Response->id, $user2->latestSurveyResponse()->id);
    }

    /** @test */
    public function user_casts_are_properly_configured()
    {
        $user = new User();
        $casts = $user->getCasts();

        $this->assertArrayHasKey('email_verified_at', $casts);
        $this->assertEquals('datetime', $casts['email_verified_at']);
        
        $this->assertArrayHasKey('password', $casts);
        $this->assertEquals('hashed', $casts['password']);
    }

    /** @test */
    public function user_uses_correct_traits()
    {
        $user = new User();
        $traits = class_uses_recursive(get_class($user));

        $this->assertContains('Illuminate\Database\Eloquent\Factories\HasFactory', $traits);
        $this->assertContains('Illuminate\Notifications\Notifiable', $traits);
        $this->assertContains('Laravel\Sanctum\HasApiTokens', $traits);
    }
}
