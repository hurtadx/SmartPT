<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\SurveyController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
| Aquí registramos las rutas de la API. Estas rutas son cargadas por el 
| RouteServiceProvider y están asignadas al grupo de middleware "api".
| ¡Disfruta construyendo tu API!
*/

// Ruta de prueba - para verificar que la API funciona
Route::get('/test', function () {
    return response()->json([
        'success' => true,
        'message' => '¡API de SmartPT funcionando correctamente!',
        'version' => '1.0.0',
        'timestamp' => now(),
    ]);
});

// Rutas de autenticación (NO requieren token)
Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
});

// Rutas protegidas (requieren token de Sanctum)
Route::middleware('auth:sanctum')->group(function () {
    
    // Rutas de autenticación para usuarios logueados
    Route::prefix('auth')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/me', [AuthController::class, 'me']);
    });
    
    // Rutas de encuestas
    Route::prefix('survey')->group(function () {
        Route::get('/questions', [SurveyController::class, 'getQuestions']);
        Route::post('/submit', [SurveyController::class, 'submitSurvey']);
        Route::get('/results', [SurveyController::class, 'getResults']);
        Route::get('/status', [SurveyController::class, 'checkStatus']);
    });
    
    // Ruta para obtener información del usuario actual
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
});

/*
|--------------------------------------------------------------------------
| Explicación de las rutas:
|--------------------------------------------------------------------------
|
| RUTAS PÚBLICAS (sin autenticación):
| POST /api/auth/register - Registrar nuevo usuario
| POST /api/auth/login    - Iniciar sesión
| GET  /api/test          - Probar que la API funciona
|
| RUTAS PROTEGIDAS (requieren token):
| POST /api/auth/logout   - Cerrar sesión
| GET  /api/auth/me       - Info del usuario actual
| GET  /api/survey/questions - Obtener preguntas de la encuesta
| POST /api/survey/submit    - Enviar respuestas de la encuesta
| GET  /api/survey/results   - Ver resultados de mi encuesta
| GET  /api/survey/status    - Verificar si ya completé la encuesta
| GET  /api/user             - Info básica del usuario
|
*/
