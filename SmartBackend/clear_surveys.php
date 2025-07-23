<?php
require 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Http\Kernel')->bootstrap();

use App\Models\SurveyResponse;

echo "=== LIMPIAR ENCUESTAS PARA TESTING ===\n\n";

$totalDeleted = SurveyResponse::count();
echo "Respuestas a eliminar: $totalDeleted\n";

if ($totalDeleted > 0) {
    SurveyResponse::truncate();
    echo "✅ Todas las respuestas han sido eliminadas\n";
    echo "Ahora puedes probar el formulario nuevamente\n";
} else {
    echo "No hay respuestas que eliminar\n";
}

echo "\n=== FIN LIMPIEZA ===\n";
