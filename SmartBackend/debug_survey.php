<?php

require_once 'vendor/autoload.php';

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\SurveyController;
use App\Http\Requests\SurveyRequest;

// Simular datos de encuesta (exactamente como los envía el frontend)
$surveyData = [
    'favorite_framework' => 'React porque es muy flexible',
    'experience_level' => 'Senior',
    'programming_languages' => ['JavaScript', 'PHP', 'Python'],
    'teamwork_rating' => 4,
    'agile_experience' => true
];

echo "=== DEBUG SURVEY SUBMISSION ===\n\n";

echo "1. Datos que se envían:\n";
print_r($surveyData);

echo "\n2. Validando campos requeridos según SurveyRequest:\n";

// Verificar cada campo según las reglas de validación
$errors = [];

// favorite_framework: required|string|max:1000
if (empty($surveyData['favorite_framework'])) {
    $errors[] = "favorite_framework es requerido";
} elseif (!is_string($surveyData['favorite_framework'])) {
    $errors[] = "favorite_framework debe ser string";
} elseif (strlen($surveyData['favorite_framework']) > 1000) {
    $errors[] = "favorite_framework no puede exceder 1000 caracteres";
}

// experience_level: required|in:Junior,Mid,Senior
if (empty($surveyData['experience_level'])) {
    $errors[] = "experience_level es requerido";
} elseif (!in_array($surveyData['experience_level'], ['Junior', 'Mid', 'Senior'])) {
    $errors[] = "experience_level debe ser Junior, Mid o Senior";
}

// programming_languages: required|array|min:1
if (!isset($surveyData['programming_languages'])) {
    $errors[] = "programming_languages es requerido";
} elseif (!is_array($surveyData['programming_languages'])) {
    $errors[] = "programming_languages debe ser un array";
} elseif (count($surveyData['programming_languages']) < 1) {
    $errors[] = "programming_languages debe tener al menos 1 elemento";
}

// programming_languages.*: in:JavaScript,PHP,Python,Java
if (isset($surveyData['programming_languages']) && is_array($surveyData['programming_languages'])) {
    $validLanguages = ['JavaScript', 'PHP', 'Python', 'Java'];
    foreach ($surveyData['programming_languages'] as $lang) {
        if (!in_array($lang, $validLanguages)) {
            $errors[] = "programming_languages contiene valor inválido: $lang";
        }
    }
}

// teamwork_rating: required|integer|min:1|max:5
if (!isset($surveyData['teamwork_rating'])) {
    $errors[] = "teamwork_rating es requerido";
} elseif (!is_integer($surveyData['teamwork_rating'])) {
    $errors[] = "teamwork_rating debe ser entero";
} elseif ($surveyData['teamwork_rating'] < 1 || $surveyData['teamwork_rating'] > 5) {
    $errors[] = "teamwork_rating debe estar entre 1 y 5";
}

// agile_experience: required|boolean
if (!isset($surveyData['agile_experience'])) {
    $errors[] = "agile_experience es requerido";
} elseif (!is_bool($surveyData['agile_experience'])) {
    $errors[] = "agile_experience debe ser boolean";
}

if (empty($errors)) {
    echo "✅ Todas las validaciones pasaron correctamente!\n";
} else {
    echo "❌ Errores de validación encontrados:\n";
    foreach ($errors as $error) {
        echo "  - $error\n";
    }
}

echo "\n3. Tipos de datos:\n";
foreach ($surveyData as $key => $value) {
    echo "  $key: " . gettype($value) . " = ";
    if (is_array($value)) {
        echo "[" . implode(", ", $value) . "]";
    } elseif (is_bool($value)) {
        echo $value ? "true" : "false";
    } else {
        echo $value;
    }
    echo "\n";
}

echo "\n=== FIN DEBUG ===\n";
