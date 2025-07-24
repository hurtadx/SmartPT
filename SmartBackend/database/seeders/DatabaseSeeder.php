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

        // Crear usuarios de prueba para evaluadores (idempotente)
        User::firstOrCreate(
            ['email' => 'admin@smartpt.com'],
            [
                'name' => 'Administrador SmartPT',
                'password' => bcrypt('password123'),
            ]
        );

        User::firstOrCreate(
            ['email' => 'user@smartpt.com'],
            [
                'name' => 'Usuario de Prueba',
                'password' => bcrypt('password123'),
            ]
        );

        User::firstOrCreate(
            ['email' => 'evaluador@smartpt.com'],
            [
                'name' => 'Evaluador Sistema',
                'password' => bcrypt('password123'),
            ]
        );

        echo "✅ Usuarios de prueba creados:\n";
        echo "   - admin@smartpt.com / password123\n";
        echo "   - user@smartpt.com / password123\n";
        echo "   - evaluador@smartpt.com / password123\n";
    }
}
