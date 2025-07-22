<?php

namespace Tests\Unit;

use App\Models\User;
use App\Models\SurveyResponse;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SurveyResponseModelTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function survey_response_has_correct_fillable_attributes()
    {
        $fillableAttributes = [
            'user_id',
            'favorite_framework',
            'experience_level',
            'programming_languages',
            'teamwork_rating',
            'agile_experience',
            'completed_at'
        ];
        
        $surveyResponse = new SurveyResponse();
        $this->assertEquals($fillableAttributes, $surveyResponse->getFillable());
    }

    /** @test */
    public function survey_response_has_correct_casts()
    {
        $surveyResponse = new SurveyResponse();
        $casts = $surveyResponse->getCasts();

        $this->assertArrayHasKey('programming_languages', $casts);
        $this->assertEquals('array', $casts['programming_languages']);
        
        $this->assertArrayHasKey('agile_experience', $casts);
        $this->assertEquals('boolean', $casts['agile_experience']);
        
        $this->assertArrayHasKey('teamwork_rating', $casts);
        $this->assertEquals('integer', $casts['teamwork_rating']);
        
        $this->assertArrayHasKey('completed_at', $casts);
        $this->assertEquals('datetime', $casts['completed_at']);
    }

    /** @test */
    public function survey_response_belongs_to_user()
    {
        $user = User::factory()->create();
        $surveyResponse = SurveyResponse::factory()->create(['user_id' => $user->id]);

        $this->assertInstanceOf(User::class, $surveyResponse->user);
        $this->assertEquals($user->id, $surveyResponse->user->id);
        $this->assertEquals($user->name, $surveyResponse->user->name);
        $this->assertEquals($user->email, $surveyResponse->user->email);
    }

    /** @test */
    public function programming_languages_is_cast_to_array()
    {
        $languages = ['PHP', 'JavaScript', 'Python'];
        
        $surveyResponse = SurveyResponse::factory()->create([
            'programming_languages' => $languages
        ]);

        $this->assertIsArray($surveyResponse->programming_languages);
        $this->assertEquals($languages, $surveyResponse->programming_languages);
    }

    /** @test */
    public function agile_experience_is_cast_to_boolean()
    {
        $surveyResponse1 = SurveyResponse::factory()->create([
            'agile_experience' => true
        ]);
        
        $surveyResponse2 = SurveyResponse::factory()->create([
            'agile_experience' => false
        ]);

        $this->assertIsBool($surveyResponse1->agile_experience);
        $this->assertTrue($surveyResponse1->agile_experience);
        
        $this->assertIsBool($surveyResponse2->agile_experience);
        $this->assertFalse($surveyResponse2->agile_experience);
    }

    /** @test */
    public function teamwork_rating_is_cast_to_integer()
    {
        $surveyResponse = SurveyResponse::factory()->create([
            'teamwork_rating' => '4' // String que debería convertirse a int
        ]);

        $this->assertIsInt($surveyResponse->teamwork_rating);
        $this->assertEquals(4, $surveyResponse->teamwork_rating);
    }

    /** @test */
    public function completed_at_is_cast_to_datetime()
    {
        $surveyResponse = SurveyResponse::factory()->create([
            'completed_at' => now()
        ]);

        $this->assertInstanceOf(\Illuminate\Support\Carbon::class, $surveyResponse->completed_at);
    }

    /** @test */
    public function is_completed_returns_false_when_completed_at_is_null()
    {
        $surveyResponse = SurveyResponse::factory()->create([
            'completed_at' => null
        ]);

        $this->assertFalse($surveyResponse->isCompleted());
    }

    /** @test */
    public function is_completed_returns_true_when_completed_at_is_set()
    {
        $surveyResponse = SurveyResponse::factory()->create([
            'completed_at' => now()
        ]);

        $this->assertTrue($surveyResponse->isCompleted());
    }

    /** @test */
    public function mark_as_completed_sets_completed_at_timestamp()
    {
        $surveyResponse = SurveyResponse::factory()->create([
            'completed_at' => null
        ]);

        $this->assertNull($surveyResponse->completed_at);
        $this->assertFalse($surveyResponse->isCompleted());

        $surveyResponse->markAsCompleted();

        $this->assertNotNull($surveyResponse->fresh()->completed_at);
        $this->assertTrue($surveyResponse->fresh()->isCompleted());
    }

    /** @test */
    public function mark_as_completed_updates_existing_completed_at()
    {
        $originalTime = now()->subHour();
        $surveyResponse = SurveyResponse::factory()->create([
            'completed_at' => $originalTime
        ]);

        $this->assertEquals($originalTime->format('Y-m-d H:i:s'), $surveyResponse->completed_at->format('Y-m-d H:i:s'));

        $surveyResponse->markAsCompleted();

        $this->assertNotEquals($originalTime->format('Y-m-d H:i:s'), $surveyResponse->fresh()->completed_at->format('Y-m-d H:i:s'));
    }

    /** @test */
    public function survey_response_can_be_created_with_all_fields()
    {
        $user = User::factory()->create();
        
        $surveyData = [
            'user_id' => $user->id,
            'favorite_framework' => 'Laravel es fantástico',
            'experience_level' => 'Senior',
            'programming_languages' => ['PHP', 'JavaScript', 'Python'],
            'teamwork_rating' => 5,
            'agile_experience' => true,
            'completed_at' => now()
        ];

        $surveyResponse = SurveyResponse::create($surveyData);

        $this->assertDatabaseHas('survey_responses', [
            'user_id' => $user->id,
            'favorite_framework' => 'Laravel es fantástico',
            'experience_level' => 'Senior',
            'teamwork_rating' => 5,
            'agile_experience' => true
        ]);

        $this->assertEquals($user->id, $surveyResponse->user_id);
        $this->assertEquals('Laravel es fantástico', $surveyResponse->favorite_framework);
        $this->assertEquals('Senior', $surveyResponse->experience_level);
        $this->assertEquals(['PHP', 'JavaScript', 'Python'], $surveyResponse->programming_languages);
        $this->assertEquals(5, $surveyResponse->teamwork_rating);
        $this->assertTrue($surveyResponse->agile_experience);
        $this->assertTrue($surveyResponse->isCompleted());
    }

    /** @test */
    public function survey_response_can_store_various_experience_levels()
    {
        $user = User::factory()->create();
        
        $levels = ['Junior', 'Mid', 'Senior'];
        
        foreach ($levels as $level) {
            $surveyResponse = SurveyResponse::factory()->create([
                'user_id' => $user->id,
                'experience_level' => $level
            ]);
            
            $this->assertEquals($level, $surveyResponse->experience_level);
        }
    }

    /** @test */
    public function survey_response_can_store_different_teamwork_ratings()
    {
        $user = User::factory()->create();
        
        for ($rating = 1; $rating <= 5; $rating++) {
            $surveyResponse = SurveyResponse::factory()->create([
                'user_id' => $user->id,
                'teamwork_rating' => $rating
            ]);
            
            $this->assertEquals($rating, $surveyResponse->teamwork_rating);
            $this->assertIsInt($surveyResponse->teamwork_rating);
        }
    }

    /** @test */
    public function survey_response_can_store_empty_programming_languages_array()
    {
        $surveyResponse = SurveyResponse::factory()->create([
            'programming_languages' => []
        ]);

        $this->assertIsArray($surveyResponse->programming_languages);
        $this->assertEmpty($surveyResponse->programming_languages);
    }

    /** @test */
    public function survey_response_can_store_single_programming_language()
    {
        $surveyResponse = SurveyResponse::factory()->create([
            'programming_languages' => ['PHP']
        ]);

        $this->assertIsArray($surveyResponse->programming_languages);
        $this->assertCount(1, $surveyResponse->programming_languages);
        $this->assertEquals(['PHP'], $surveyResponse->programming_languages);
    }

    /** @test */
    public function survey_response_uses_has_factory_trait()
    {
        $surveyResponse = new SurveyResponse();
        $traits = class_uses_recursive(get_class($surveyResponse));

        $this->assertContains('Illuminate\Database\Eloquent\Factories\HasFactory', $traits);
    }

    /** @test */
    public function survey_response_factory_creates_valid_instance()
    {
        $user = User::factory()->create();
        $surveyResponse = SurveyResponse::factory()->create(['user_id' => $user->id]);

        $this->assertInstanceOf(SurveyResponse::class, $surveyResponse);
        $this->assertNotNull($surveyResponse->id);
        $this->assertEquals($user->id, $surveyResponse->user_id);
        $this->assertNotNull($surveyResponse->favorite_framework);
        $this->assertNotNull($surveyResponse->experience_level);
        $this->assertIsArray($surveyResponse->programming_languages);
        $this->assertIsInt($surveyResponse->teamwork_rating);
        $this->assertIsBool($surveyResponse->agile_experience);
    }
}
