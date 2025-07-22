<?php

namespace Database\Factories;

use App\Models\SurveyResponse;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\SurveyResponse>
 */
class SurveyResponseFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = SurveyResponse::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $frameworks = [
            'Laravel porque es muy potente y elegante',
            'React por su flexibilidad y ecosistema',
            'Vue.js es increíblemente fácil de aprender',
            'Angular ofrece una estructura muy robusta',
            'Express.js por su simplicidad y velocidad'
        ];

        $experienceLevels = ['Junior', 'Mid', 'Senior'];
        
        $programmingLanguages = [
            ['JavaScript', 'PHP'],
            ['PHP', 'Python'],
            ['JavaScript', 'Python', 'Java'],
            ['PHP', 'JavaScript', 'Python'],
            ['Java', 'Python'],
            ['JavaScript'],
            ['PHP']
        ];

        return [
            'user_id' => User::factory(),
            'favorite_framework' => $this->faker->randomElement($frameworks),
            'experience_level' => $this->faker->randomElement($experienceLevels),
            'programming_languages' => $this->faker->randomElement($programmingLanguages),
            'teamwork_rating' => $this->faker->numberBetween(1, 5),
            'agile_experience' => $this->faker->boolean(),
            'completed_at' => $this->faker->boolean(80) ? $this->faker->dateTimeThisYear() : null,
        ];
    }

    /**
     * Indicate that the survey response is completed.
     */
    public function completed(): static
    {
        return $this->state(fn (array $attributes) => [
            'completed_at' => $this->faker->dateTimeThisYear(),
        ]);
    }

    /**
     * Indicate that the survey response is not completed.
     */
    public function incomplete(): static
    {
        return $this->state(fn (array $attributes) => [
            'completed_at' => null,
        ]);
    }

    /**
     * Survey response for a senior developer.
     */
    public function senior(): static
    {
        return $this->state(fn (array $attributes) => [
            'experience_level' => 'Senior',
            'programming_languages' => ['PHP', 'JavaScript', 'Python', 'Java'],
            'teamwork_rating' => $this->faker->numberBetween(4, 5),
            'agile_experience' => true,
        ]);
    }

    /**
     * Survey response for a junior developer.
     */
    public function junior(): static
    {
        return $this->state(fn (array $attributes) => [
            'experience_level' => 'Junior',
            'programming_languages' => $this->faker->randomElement([
                ['JavaScript'],
                ['PHP'],
                ['Python'],
                ['JavaScript', 'PHP']
            ]),
            'teamwork_rating' => $this->faker->numberBetween(1, 3),
        ]);
    }

    /**
     * Survey response for a mid-level developer.
     */
    public function mid(): static
    {
        return $this->state(fn (array $attributes) => [
            'experience_level' => 'Mid',
            'programming_languages' => $this->faker->randomElement([
                ['JavaScript', 'PHP'],
                ['PHP', 'Python'],
                ['JavaScript', 'Python'],
                ['PHP', 'JavaScript', 'Python']
            ]),
            'teamwork_rating' => $this->faker->numberBetween(2, 4),
        ]);
    }
}
