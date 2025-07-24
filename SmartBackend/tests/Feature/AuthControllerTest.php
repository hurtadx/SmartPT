<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class AuthControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    #[\PHPUnit\Framework\Attributes\Test]
    public function user_can_register_with_valid_data()
    {
        $userData = [
            'name' => 'André Hurtado',
            'email' => 'andre@example.com',
            'password' => 'SecurePassword123',
            'password_confirmation' => 'SecurePassword123'
        ];

        $response = $this->postJson('/api/auth/register', $userData);

        $response->assertStatus(201)
                ->assertJsonStructure([
                    'success',
                    'message',
                    'data' => [
                        'user' => [
                            'id',
                            'name',
                            'email'
                        ],
                        'token',
                        'token_type'
                    ]
                ]);

        // Verificar que el usuario se creó en la base de datos
        $this->assertDatabaseHas('users', [
            'name' => 'André Hurtado',
            'email' => 'andre@example.com'
        ]);

        // Verificar que la respuesta contiene datos correctos
        $responseData = $response->json();
        $this->assertTrue($responseData['success']);
        $this->assertEquals('Bearer', $responseData['data']['token_type']);
        $this->assertNotEmpty($responseData['data']['token']);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function user_registration_requires_valid_email()
    {
        $userData = [
            'name' => 'André Hurtado',
            'email' => 'invalid-email', // Email inválido
            'password' => 'SecurePassword123',
            'password_confirmation' => 'SecurePassword123'
        ];

        $response = $this->postJson('/api/auth/register', $userData);

        $response->assertStatus(422)
                ->assertJsonValidationErrors(['email']);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function user_registration_requires_unique_email()
    {
        // Crear usuario existente
        User::factory()->create(['email' => 'andre@example.com']);

        $userData = [
            'name' => 'Otro Usuario',
            'email' => 'andre@example.com', // Email ya existe
            'password' => 'SecurePassword123',
            'password_confirmation' => 'SecurePassword123'
        ];

        $response = $this->postJson('/api/auth/register', $userData);

        $response->assertStatus(422)
                ->assertJsonValidationErrors(['email']);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function user_registration_requires_password_confirmation()
    {
        $userData = [
            'name' => 'André Hurtado',
            'email' => 'andre@example.com',
            'password' => 'SecurePassword123',
            'password_confirmation' => 'DifferentPassword123' // No coincide
        ];

        $response = $this->postJson('/api/auth/register', $userData);

        $response->assertStatus(422)
                ->assertJsonValidationErrors(['password']);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function user_registration_requires_minimum_password_length()
    {
        $userData = [
            'name' => 'André Hurtado',
            'email' => 'andre@example.com',
            'password' => '123', // Muy corta
            'password_confirmation' => '123'
        ];

        $response = $this->postJson('/api/auth/register', $userData);

        $response->assertStatus(422)
                ->assertJsonValidationErrors(['password']);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function user_can_login_with_valid_credentials()
    {
        // Crear usuario con contraseña conocida
        $user = User::factory()->create([
            'email' => 'andre@example.com',
            'password' => Hash::make('SecurePassword123')
        ]);

        $loginData = [
            'email' => 'andre@example.com',
            'password' => 'SecurePassword123'
        ];

        $response = $this->postJson('/api/auth/login', $loginData);

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'success',
                    'message',
                    'data' => [
                        'user' => [
                            'id',
                            'name',
                            'email',
                            'has_completed_survey'
                        ],
                        'token',
                        'token_type'
                    ]
                ]);

        $responseData = $response->json();
        $this->assertTrue($responseData['success']);
        $this->assertEquals($user->id, $responseData['data']['user']['id']);
        $this->assertEquals($user->email, $responseData['data']['user']['email']);
        $this->assertFalse($responseData['data']['user']['has_completed_survey']);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function user_cannot_login_with_invalid_credentials()
    {
        // Crear usuario
        User::factory()->create([
            'email' => 'andre@example.com',
            'password' => Hash::make('CorrectPassword123')
        ]);

        $loginData = [
            'email' => 'andre@example.com',
            'password' => 'WrongPassword123' // Contraseña incorrecta
        ];

        $response = $this->postJson('/api/auth/login', $loginData);

        $response->assertStatus(422)
                ->assertJson([
                    'success' => false,
                    'message' => 'Credenciales inválidas'
                ]);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function user_cannot_login_with_nonexistent_email()
    {
        $loginData = [
            'email' => 'nonexistent@example.com',
            'password' => 'SomePassword123'
        ];

        $response = $this->postJson('/api/auth/login', $loginData);

        $response->assertStatus(422)
                ->assertJson([
                    'success' => false,
                    'message' => 'Credenciales inválidas'
                ]);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function login_requires_email_and_password()
    {
        $response = $this->postJson('/api/auth/login', []);

        $response->assertStatus(422)
                ->assertJsonValidationErrors(['email', 'password']);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function login_deletes_old_tokens_and_creates_new_one()
    {
        $user = User::factory()->create([
            'email' => 'andre@example.com',
            'password' => Hash::make('SecurePassword123')
        ]);

        // Crear algunos tokens existentes
        $user->createToken('old_token_1')->plainTextToken;
        $user->createToken('old_token_2')->plainTextToken;
        
        $this->assertEquals(2, $user->tokens()->count());

        $loginData = [
            'email' => 'andre@example.com',
            'password' => 'SecurePassword123'
        ];

        $response = $this->postJson('/api/auth/login', $loginData);

        $response->assertStatus(200);
        
        // Verificar que los tokens viejos fueron eliminados y se creó uno nuevo
        $this->assertEquals(1, $user->fresh()->tokens()->count());
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function authenticated_user_can_logout()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $response = $this->postJson('/api/auth/logout');

        $response->assertStatus(200)
                ->assertJson([
                    'success' => true,
                    'message' => '¡Sesión cerrada exitosamente!'
                ]);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function logout_deletes_current_access_token()
    {
        $user = User::factory()->create([
            'email' => 'andre@example.com',
            'password' => Hash::make('SecurePassword123')
        ]);

        // Crear un token directamente para el usuario
        $token = $user->createToken('test-token')->plainTextToken;
        
        // Verificar que el usuario tiene exactamente 1 token
        $tokensBeforeLogout = $user->fresh()->tokens()->count();
        $this->assertEquals(1, $tokensBeforeLogout);

        // Hacer logout usando el token
        $response = $this->postJson('/api/auth/logout', [], [
            'Authorization' => 'Bearer ' . $token
        ]);

        $response->assertStatus(200)
                ->assertJson([
                    'success' => true,
                    'message' => '¡Sesión cerrada exitosamente!'
                ]);

        // Verificar que el token fue eliminado
        $tokensAfterLogout = $user->fresh()->tokens()->count();
        $this->assertEquals(0, $tokensAfterLogout);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function unauthenticated_user_cannot_logout()
    {
        $response = $this->postJson('/api/auth/logout');

        $response->assertStatus(401);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function authenticated_user_can_get_profile()
    {
        $user = User::factory()->create([
            'name' => 'André Hurtado',
            'email' => 'andre@example.com'
        ]);
        Sanctum::actingAs($user);

        $response = $this->getJson('/api/auth/me');

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'success',
                    'data' => [
                        'user' => [
                            'id',
                            'name',
                            'email',
                            'has_completed_survey'
                        ]
                    ]
                ]);

        $responseData = $response->json();
        $this->assertTrue($responseData['success']);
        $this->assertEquals($user->name, $responseData['data']['user']['name']);
        $this->assertEquals($user->email, $responseData['data']['user']['email']);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function unauthenticated_user_cannot_get_profile()
    {
        $response = $this->getJson('/api/auth/me');

        $response->assertStatus(401);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function registration_password_is_hashed()
    {
        $userData = [
            'name' => 'André Hurtado',
            'email' => 'andre@example.com',
            'password' => 'PlainTextPassword123',
            'password_confirmation' => 'PlainTextPassword123'
        ];

        $response = $this->postJson('/api/auth/register', $userData);

        $response->assertStatus(201);

        $user = User::where('email', 'andre@example.com')->first();
        
        // Verificar que la contraseña fue hasheada y no está en texto plano
        $this->assertNotEquals('PlainTextPassword123', $user->password);
        $this->assertTrue(Hash::check('PlainTextPassword123', $user->password));
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function login_includes_survey_completion_status()
    {
        $user = User::factory()->create([
            'email' => 'andre@example.com',
            'password' => Hash::make('SecurePassword123')
        ]);

        $loginData = [
            'email' => 'andre@example.com',
            'password' => 'SecurePassword123'
        ];

        $response = $this->postJson('/api/auth/login', $loginData);

        $response->assertStatus(200);
        
        $responseData = $response->json();
        $this->assertArrayHasKey('has_completed_survey', $responseData['data']['user']);
        $this->assertIsBool($responseData['data']['user']['has_completed_survey']);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function registration_creates_user_with_correct_attributes()
    {
        $userData = [
            'name' => 'André Hurtado',
            'email' => 'andre@example.com',
            'password' => 'SecurePassword123',
            'password_confirmation' => 'SecurePassword123'
        ];

        $response = $this->postJson('/api/auth/register', $userData);

        $response->assertStatus(201);

        $user = User::where('email', 'andre@example.com')->first();
        
        $this->assertNotNull($user);
        $this->assertEquals('André Hurtado', $user->name);
        $this->assertEquals('andre@example.com', $user->email);
        $this->assertNotNull($user->password);
        $this->assertNull($user->email_verified_at);
    }
}
