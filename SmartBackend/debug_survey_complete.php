<?php
require 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Http\Kernel')->bootstrap();

use App\Models\User;
use App\Http\Controllers\SurveyController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

echo "=== DEBUG SURVEY SUBMISSION COMPLETO ===\n";

// Simular datos del formulario exactamente como los envía React
$formData = [
    'favorite_framework' => 'React porque es muy flexible',
    'experience_level' => 'Senior',
    'programming_languages' => ['JavaScript', 'PHP', 'Python'],
    'teamwork_rating' => 4,
    'agile_experience' => true
];

echo "1. Datos que se envían desde React:\n";
print_r($formData);

// Crear un usuario de prueba
$user = User::first();
if (!$user) {
    echo "❌ No hay usuarios en la base de datos\n";
    exit;
}

// Simular autenticación
auth()->login($user);
echo "\n✅ Usuario autenticado: " . $user->email . "\n";

echo "\n2. Probando validación con Laravel Validator:\n";

// Usar exactamente las mismas reglas que SurveyRequest
$rules = [
    'favorite_framework' => 'required|string|max:1000',
    'experience_level' => 'required|string|in:Junior,Mid,Senior',
    'programming_languages' => 'required|array|min:1',
    'programming_languages.*' => 'string|in:JavaScript,TypeScript,Python,PHP,Java,C#,Go,Rust,Swift,Kotlin',
    'teamwork_rating' => 'required|integer|between:1,5',
    'agile_experience' => 'required|boolean'
];

$messages = [
    'favorite_framework.required' => 'Debes responder cuál es tu framework favorito.',
    'experience_level.required' => 'Debes seleccionar tu nivel de experiencia.',
    'programming_languages.required' => 'Debes seleccionar al menos un lenguaje de programación.',
    'teamwork_rating.required' => 'Debes calificar tu capacidad de trabajo en equipo.',
    'agile_experience.required' => 'Debes responder si tienes experiencia con metodologías ágiles.',
];

$validator = Validator::make($formData, $rules, $messages);

if ($validator->fails()) {
    echo "❌ Validación falló:\n";
    foreach ($validator->errors()->all() as $error) {
        echo "  - $error\n";
    }
    echo "\nErrores por campo:\n";
    foreach ($validator->errors()->toArray() as $field => $fieldErrors) {
        echo "  $field: " . implode(', ', $fieldErrors) . "\n";
    }
} else {
    echo "✅ Validación con Validator exitosa!\n";
}

echo "\n3. Simulando request HTTP como el que envía React:\n";

// Crear request simulado exactamente como lo haría axios
$request = Request::create('/api/survey/submit', 'POST', $formData);
$request->headers->set('Content-Type', 'application/json');
$request->headers->set('Accept', 'application/json');
$request->headers->set('Authorization', 'Bearer fake-token');

echo "Headers del request:\n";
echo "  Content-Type: " . $request->header('Content-Type') . "\n";
echo "  Accept: " . $request->header('Accept') . "\n";

echo "Datos del request:\n";
print_r($request->all());

echo "\n4. Probando el controlador directamente:\n";

try {
    $controller = new SurveyController();
    $response = $controller->submitSurvey($request);
    echo "✅ Controlador ejecutado exitosamente!\n";
    echo "Status code: " . $response->getStatusCode() . "\n";
    echo "Respuesta: " . $response->getContent() . "\n";
} catch (Exception $e) {
    echo "❌ Error en controlador: " . $e->getMessage() . "\n";
    echo "Archivo: " . $e->getFile() . " línea " . $e->getLine() . "\n";
    
    // Si es error de validación, mostrar detalles
    if (method_exists($e, 'errors')) {
        echo "Errores de validación:\n";
        print_r($e->errors());
    }
}

echo "\n5. Verificando tipos de datos:\n";
foreach ($formData as $key => $value) {
    $type = gettype($value);
    echo "  $key: $type";
    if (is_array($value)) {
        echo " = [" . implode(', ', $value) . "]";
    } elseif (is_bool($value)) {
        echo " = " . ($value ? 'true' : 'false');
    } else {
        echo " = $value";
    }
    echo "\n";
}

echo "\n6. Verificando si el modelo Survey existe:\n";
try {
    $surveyClass = 'App\\Models\\Survey';
    if (class_exists($surveyClass)) {
        echo "✅ Modelo Survey existe\n";
        
        // Verificar las columnas que se pueden llenar
        $survey = new $surveyClass();
        if (method_exists($survey, 'getFillable')) {
            echo "Campos fillable: " . implode(', ', $survey->getFillable()) . "\n";
        }
    } else {
        echo "❌ Modelo Survey no existe\n";
    }
} catch (Exception $e) {
    echo "❌ Error verificando modelo: " . $e->getMessage() . "\n";
}

echo "\n=== FIN DEBUG COMPLETO ===\n";
