<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Spatie\Permission\Models\Role;

class agregarusuarios extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Inserta los usuarios sin el campo 'role'
        $usuarios = [
            [
                'name' => 'Tecnico Uno',
                'email' => 'tecnico1@example.com',
                'password' => Hash::make('password123'),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Tecnico Dos',
                'email' => 'tecnico2@example.com',
                'password' => Hash::make('password123'),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Tecnico Tres',
                'email' => 'tecnico3@example.com',
                'password' => Hash::make('password123'),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Tecnico Cuatro',
                'email' => 'tecnico4@example.com',
                'password' => Hash::make('password123'),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Tecnico Cinco',
                'email' => 'tecnico5@example.com',
                'password' => Hash::make('password123'),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Tecnico Seis',
                'email' => 'tecnico6@example.com',
                'password' => Hash::make('password123'),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Tecnico Siete',
                'email' => 'tecnico7@example.com',
                'password' => Hash::make('password123'),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Tecnico Ocho',
                'email' => 'tecnico8@example.com',
                'password' => Hash::make('password123'),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Tecnico Nueve',
                'email' => 'tecnico9@example.com',
                'password' => Hash::make('password123'),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Tecnico 10',
                'email' => 'tecnico10@example.com',
                'password' => Hash::make('password123'),
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('users')->insert($usuarios);

        // Asigna el rol 'Tecnico' a todos los usuarios insertados
        $role = Role::firstOrCreate(['name' => 'Tecnico']);
        $emails = array_column($usuarios, 'email');
        $users = User::whereIn('email', $emails)->get();
        foreach ($users as $user) {
            $user->assignRole($role);
        }
    }
}