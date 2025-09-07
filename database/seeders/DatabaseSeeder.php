<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            PermissionSeeder::class,
            ItilCategoriasSeeder::class,
            CategoriaDispositivoSeeder::class,
            DispositivoSeeder::class,
            agregarusuarios::class,
            AreasSeeder::class,
            SlaFactoresSeeder::class,  // ¡IMPORTANTE! Debe ir ANTES de SlaSeeder
            SlaSeeder::class,
            ActualizarUsuariosConAreas::class,
            TicketsSeederCorregido::class,      // Crear tickets al final con todos los datos listos
        ]);
    }
}
