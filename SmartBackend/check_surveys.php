<?php
require 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Http\Kernel')->bootstrap();

use App\Models\SurveyResponse;
use App\Models\User;

echo "=== VERIFICACIÓN DE ENCUESTAS ===\n\n";

// Contar respuestas totales
$totalSurveys = SurveyResponse::count();
echo "Total de respuestas de encuesta: $totalSurveys\n\n";

if ($totalSurveys > 0) {
    echo "Respuestas existentes:\n";
    $surveys = SurveyResponse::with('user')->get();
    
    foreach ($surveys as $survey) {
        echo "- Usuario: " . $survey->user->email . "\n";
        echo "  Completado: " . $survey->completed_at . "\n";
        echo "  Framework favorito: " . $survey->favorite_framework . "\n";
        echo "  Nivel de experiencia: " . $survey->experience_level . "\n\n";
    }
}

// Verificar el usuario actual logueado
echo "Usuarios en el sistema:\n";
$users = User::all();
foreach ($users as $user) {
    $hasCompleted = $user->hasCompletedSurvey();
    echo "- " . $user->email . " (Completó encuesta: " . ($hasCompleted ? "SÍ" : "NO") . ")\n";
}

echo "\n=== FIN VERIFICACIÓN ===\n";
