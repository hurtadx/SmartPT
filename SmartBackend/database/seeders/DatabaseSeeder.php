<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Crear usuarios de prueba para evaluadores
        User::factory()->create([
            'name' => 'Administrador SmartPT',
            'email' => 'admin@smartpt.com',
            'password' => bcrypt('password123'),
        ]);

        User::factory()->create([
            'name' => 'Usuario de Prueba',
            'email' => 'user@smartpt.com',
            'password' => bcrypt('password123'),
        ]);

        User::factory()->create([
            'name' => 'Evaluador Sistema',
            'email' => 'evaluador@smartpt.com',
            'password' => bcrypt('password123'),
        ]);

        echo "✅ Usuarios de prueba creados:\n";
        echo "   - admin@smartpt.com / password123\n";
        echo "   - user@smartpt.com / password123\n";
        echo "   - evaluador@smartpt.com / password123\n";
    }
}
