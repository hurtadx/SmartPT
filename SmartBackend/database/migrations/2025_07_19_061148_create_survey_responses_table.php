<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('survey_responses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            
            // Las 5 preguntas de la encuesta
            $table->text('favorite_framework')->nullable(); // Pregunta 1: texto abierto
            $table->enum('experience_level', ['Junior', 'Mid', 'Senior'])->nullable(); // Pregunta 2: opción única
            $table->json('programming_languages')->nullable(); // Pregunta 3: selección múltiple (JavaScript, PHP, Python, Java)
            $table->integer('teamwork_rating')->nullable(); // Pregunta 4: escala 1-5
            $table->boolean('agile_experience')->nullable(); // Pregunta 5: Sí/No
            
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('survey_responses');
    }
};
