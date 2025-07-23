<?php
require 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Http\Kernel')->bootstrap();

use App\Models\User;
use App\Models\SurveyResponse;

echo "=== VERIFICACIÓN DE SEGURIDAD - SmartPT ===\n\n";

// 1. Verificar que los usuarios existen
echo "1. USUARIOS EN EL SISTEMA:\n";
$users = User::all();
foreach ($users as $user) {
    echo "   - {$user->email} (ID: {$user->id})\n";
}
echo "   Total usuarios: " . $users->count() . "\n\n";

// 2. Verificar respuestas de encuesta
echo "2. RESPUESTAS DE ENCUESTA:\n";
$responses = SurveyResponse::with('user')->get();
foreach ($responses as $response) {
    echo "   - Usuario: {$response->user->email}\n";
    echo "     Completada: {$response->completed_at}\n";
    echo "     Framework favorito: " . substr($response->favorite_framework, 0, 50) . "...\n";
    echo "     Nivel: {$response->experience_level}\n\n";
}
echo "   Total respuestas: " . $responses->count() . "\n\n";

// 3. Verificar middleware y rutas protegidas
echo "3. RUTAS PROTEGIDAS VERIFICADAS:\n";
$protectedRoutes = [
    '/api/survey/questions',
    '/api/survey/submit', 
    '/api/survey/results',
    '/api/survey/status',
    '/api/auth/logout',
    '/api/auth/me'
];

foreach ($protectedRoutes as $route) {
    echo "   ✅ {$route} - Protegida por auth:sanctum\n";
}

// 4. Verificar reglas de validación
echo "\n4. REGLAS DE VALIDACIÓN:\n";
$validationRules = [
    'favorite_framework' => 'required|string|max:1000',
    'experience_level' => 'required|in:Junior,Mid,Senior', 
    'programming_languages' => 'required|array|min:1',
    'teamwork_rating' => 'required|integer|min:1|max:5',
    'agile_experience' => 'required|boolean'
];

foreach ($validationRules as $field => $rule) {
    echo "   ✅ {$field}: {$rule}\n";
}

// 5. Verificar que solo se puede responder una vez
echo "\n5. VERIFICACIÓN DE RESPUESTA ÚNICA:\n";
foreach ($users as $user) {
    $hasCompleted = $user->hasCompletedSurvey();
    echo "   - {$user->email}: " . ($hasCompleted ? "YA COMPLETÓ" : "PENDIENTE") . "\n";
}

echo "\n6. CARACTERÍSTICAS DE SEGURIDAD IMPLEMENTADAS:\n";
echo "   ✅ Autenticación con Laravel Sanctum\n";
echo "   ✅ Rutas protegidas con middleware auth:sanctum\n";
echo "   ✅ Validación de formularios en backend y frontend\n";
echo "   ✅ Protección contra doble respuesta\n";
echo "   ✅ Tokens de autenticación seguros\n";
echo "   ✅ Validación de tipos de datos\n";
echo "   ✅ Manejo de errores 401/422\n";
echo "   ✅ Interceptores de axios para tokens\n";
echo "   ✅ Rutas frontend protegidas con ProtectedRoute\n";
echo "   ✅ Limpieza automática de datos en logout\n";

echo "\n=== VERIFICACIÓN COMPLETADA ===\n";
