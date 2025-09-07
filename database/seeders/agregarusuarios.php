<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Area;
use Spatie\Permission\Models\Role;

class agregarusuarios extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('🚀 Iniciando creación de usuarios con roles...');

        // 1. Crear Admin
        $this->createAdmin();

        // 2. Crear Técnicos
        $this->createTechnicians();

        // 3. Crear Usuarios finales
        $this->createEndUsers();

        $this->command->info('✅ Usuarios creados exitosamente!');
        $this->showSummary();
    }

    private function createAdmin()
    {
        $this->command->info('👨‍💼 Creando Administrador...');

        // Obtener área de Gerencia Municipal
        $gerenciaMunicipal = Area::where('nombre', 'GERENCIA MUNICIPAL')->first();

        $admin = User::firstOrCreate(
            ['email' => 'admin@gestion.com'],
            [
                'name' => 'Administrador Principal',
                'email' => 'admin@gestion.com',
                'password' => Hash::make('admin123'),
                'email_verified_at' => now(),
                'area_id' => $gerenciaMunicipal?->id,
            ]
        );

        $adminRole = Role::firstOrCreate(['name' => 'Admin']);
        if (!$admin->hasRole('Admin')) {
            $admin->assignRole($adminRole);
            $this->command->line("✅ Admin creado: {$admin->email} - Área: " . ($admin->area?->nombre ?? 'Sin área'));
        } else {
            $this->command->line("ℹ️  Admin ya existe: {$admin->email}");
        }
    }

    private function createTechnicians()
    {
        $this->command->info('👨‍🔧 Creando 5 Técnicos...');

        // Obtener área de Tecnologías de la Información
        $areaIT = Area::where('nombre', 'OFICINA DE TECNOLOGÍAS DE LA INFORMACIÓN')->first();

        $technicians = [
            [
                'name' => 'Juan Pérez Martínez',
                'email' => 'juan.perez@gestion.com',
                'specialty' => 'Hardware y Redes'
            ],
            [
                'name' => 'María García López',
                'email' => 'maria.garcia@gestion.com',
                'specialty' => 'Software y Aplicaciones'
            ],
            [
                'name' => 'Carlos Rodríguez Silva',
                'email' => 'carlos.rodriguez@gestion.com',
                'specialty' => 'Infraestructura y Servidores'
            ],
            [
                'name' => 'Ana Torres Vega',
                'email' => 'ana.torres@gestion.com',
                'specialty' => 'Soporte General'
            ],
            [
                'name' => 'Pedro Morales Castro',
                'email' => 'pedro.morales@gestion.com',
                'specialty' => 'Seguridad y Accesos'
            ]
        ];

        $techRole = Role::firstOrCreate(['name' => 'Tecnico']);

        foreach ($technicians as $techData) {
            $technician = User::firstOrCreate(
                ['email' => $techData['email']],
                [
                    'name' => $techData['name'],
                    'email' => $techData['email'],
                    'password' => Hash::make('tecnico123'),
                    'email_verified_at' => now(),
                    'area_id' => $areaIT?->id,
                ]
            );

            if (!$technician->hasRole('Tecnico')) {
                $technician->assignRole($techRole);
                $this->command->line("✅ Técnico creado: {$technician->name} ({$techData['specialty']}) - Área: " . ($technician->area?->nombre ?? 'Sin área'));
            } else {
                $this->command->line("ℹ️  Técnico ya existe: {$technician->name}");
            }
        }
    }

    private function createEndUsers()
    {
        $this->command->info('👥 Creando 20 Usuarios finales...');

        // Mapear departamentos a áreas reales de la municipalidad
        $departmentAreaMap = $this->getDepartmentAreaMapping();

        $users = [
            ['name' => 'Roberto Sánchez', 'dept' => 'Rentas'],
            ['name' => 'Laura Fernández', 'dept' => 'Desarrollo Social'],
            ['name' => 'Miguel Herrera', 'dept' => 'Recursos Humanos'],
            ['name' => 'Carmen Jiménez', 'dept' => 'Contabilidad'],
            ['name' => 'David López', 'dept' => 'Servicios Públicos'],
            ['name' => 'Elena Martín', 'dept' => 'Secretaría General'],
            ['name' => 'Francisco Ruiz', 'dept' => 'Fiscalización Tributaria'],
            ['name' => 'Adriana Castro', 'dept' => 'Desarrollo Económico'],
            ['name' => 'José Vargas', 'dept' => 'Recursos Humanos'],
            ['name' => 'Mónica Delgado', 'dept' => 'Contabilidad'],
            ['name' => 'Andrés Molina', 'dept' => 'Obras Públicas'],
            ['name' => 'Patricia Ramos', 'dept' => 'Tesorería'],
            ['name' => 'Raúl Mendoza', 'dept' => 'Desarrollo Territorial'],
            ['name' => 'Silvia Aguilar', 'dept' => 'Programas Sociales'],
            ['name' => 'Javier Ortiz', 'dept' => 'Abastecimiento'],
            ['name' => 'Daniela Flores', 'dept' => 'Gestión Ambiental'],
            ['name' => 'Álvaro Guerrero', 'dept' => 'Seguridad Ciudadana'],
            ['name' => 'Lucía Navarro', 'dept' => 'Licencias de Funcionamiento'],
            ['name' => 'Sergio Medina', 'dept' => 'Control Sanitario'],
            ['name' => 'Valentina Cruz', 'dept' => 'Registro Civil']
        ];

        $userRole = Role::firstOrCreate(['name' => 'Usuario']);

        foreach ($users as $index => $userData) {
            $email = $this->generateEmail($userData['name']);
            $areaId = $departmentAreaMap[$userData['dept']] ?? null;

            $user = User::firstOrCreate(
                ['email' => $email],
                [
                    'name' => $userData['name'],
                    'email' => $email,
                    'password' => Hash::make('usuario123'),
                    'email_verified_at' => now(),
                    'area_id' => $areaId,
                ]
            );

            if (!$user->hasRole('Usuario')) {
                $user->assignRole($userRole);
                $this->command->line("✅ Usuario creado: {$user->name} - {$userData['dept']} - Área: " . ($user->area?->nombre ?? 'Sin área') . " ({$email})");
            } else {
                $this->command->line("ℹ️  Usuario ya existe: {$user->name}");
            }
        }
    }

    private function getDepartmentAreaMapping()
    {
        // Crear mapeo de departamentos a IDs de áreas
        $areas = Area::whereIn('nombre', [
            'SUB GERENCIA DE RENTAS',
            'SUB GERENCIA DE SERVICIOS SOCIALES',
            'OFICINA DE GESTIÓN DE RECURSOS HUMANOS',
            'OFICINA DE CONTABILIDAD',
            'SUB GERENCIA DE SERVICIOS PÚBLICOS',
            'OFICINA DE SECRETARÍA GENERAL: ATENCIÓN AL CIUDADANO Y GESTIÓN DOCUMENTARIA',
            'SUB GERENCIA DE FISCALIZACIÓN TRIBUTARIA',
            'SUB GERENCIA DE DESARROLLO ECONÓMICO Y PRODUCTIVO',
            'Área de Obras Públicas',
            'OFICINA DE TESORERÍA',
            'SUB GERENCIA DE DESARROLLO TERRITORIAL',
            'SUB GERENCIA DE PROGRAMAS SOCIALES',
            'OFICINA DE ABASTECIMIENTO',
            'SUB GERENCIA AMBIENTAL',
            'Área de Seguridad Ciudadana y Serenazgo',
            'Área de Licencias de Funcionamiento',
            'Área de Control Sanitario',
            'Área de Registro Civil y Cementerio'
        ])->pluck('id', 'nombre');

        return [
            'Rentas' => $areas['SUB GERENCIA DE RENTAS'] ?? null,
            'Desarrollo Social' => $areas['SUB GERENCIA DE SERVICIOS SOCIALES'] ?? null,
            'Recursos Humanos' => $areas['OFICINA DE GESTIÓN DE RECURSOS HUMANOS'] ?? null,
            'Contabilidad' => $areas['OFICINA DE CONTABILIDAD'] ?? null,
            'Servicios Públicos' => $areas['SUB GERENCIA DE SERVICIOS PÚBLICOS'] ?? null,
            'Secretaría General' => $areas['OFICINA DE SECRETARÍA GENERAL: ATENCIÓN AL CIUDADANO Y GESTIÓN DOCUMENTARIA'] ?? null,
            'Fiscalización Tributaria' => $areas['SUB GERENCIA DE FISCALIZACIÓN TRIBUTARIA'] ?? null,
            'Desarrollo Económico' => $areas['SUB GERENCIA DE DESARROLLO ECONÓMICO Y PRODUCTIVO'] ?? null,
            'Obras Públicas' => $areas['Área de Obras Públicas'] ?? null,
            'Tesorería' => $areas['OFICINA DE TESORERÍA'] ?? null,
            'Desarrollo Territorial' => $areas['SUB GERENCIA DE DESARROLLO TERRITORIAL'] ?? null,
            'Programas Sociales' => $areas['SUB GERENCIA DE PROGRAMAS SOCIALES'] ?? null,
            'Abastecimiento' => $areas['OFICINA DE ABASTECIMIENTO'] ?? null,
            'Gestión Ambiental' => $areas['SUB GERENCIA AMBIENTAL'] ?? null,
            'Seguridad Ciudadana' => $areas['Área de Seguridad Ciudadana y Serenazgo'] ?? null,
            'Licencias de Funcionamiento' => $areas['Área de Licencias de Funcionamiento'] ?? null,
            'Control Sanitario' => $areas['Área de Control Sanitario'] ?? null,
            'Registro Civil' => $areas['Área de Registro Civil y Cementerio'] ?? null,
        ];
    }

    private function generateEmail($name)
    {
        // Convertir nombre a email
        $email = strtolower($name);
        $email = str_replace(['á', 'é', 'í', 'ó', 'ú', 'ñ'], ['a', 'e', 'i', 'o', 'u', 'n'], $email);
        $email = str_replace(' ', '.', $email);
        return $email . '@empresa.com';
    }

    private function showSummary()
    {
        $this->command->info('📊 RESUMEN DE USUARIOS CREADOS:');
        $this->command->line('');

        $superAdminCount = User::role('Super Admin')->count();
        $adminCount = User::role('Admin')->count();
        $techCount = User::role('Tecnico')->count();
        $userCount = User::role('Usuario')->count();
        $total = User::count();

        $this->command->table(
            ['Rol', 'Cantidad', 'Credenciales'],
            [
                ['Super Admin', $superAdminCount, 'Ya existe'],
                ['Admin', $adminCount, 'admin@gestion.com / admin123'],
                ['Técnicos', $techCount, 'email del técnico / tecnico123'],
                ['Usuarios', $userCount, 'email del usuario / usuario123'],
                ['TOTAL', $total, '']
            ]
        );

        $this->command->info('🔑 ACCESOS DE PRUEBA:');
        $this->command->line('👨‍💼 Admin: admin@gestion.com / admin123');
        $this->command->line('👨‍🔧 Técnico: juan.perez@gestion.com / tecnico123');
        $this->command->line('👤 Usuario: roberto.sanchez@empresa.com / usuario123');

        $this->command->info('');
        $this->command->info('🏢 DISTRIBUCIÓN POR ÁREAS:');

        $usersWithAreas = User::with('area')->whereNotNull('area_id')->get();
        $areaDistribution = $usersWithAreas->groupBy('area.nombre');

        foreach ($areaDistribution as $areaName => $users) {
            $this->command->line("📂 {$areaName}: {$users->count()} usuario(s)");
        }

        $usersWithoutArea = User::whereNull('area_id')->count();
        if ($usersWithoutArea > 0) {
            $this->command->line("⚠️  Sin área asignada: {$usersWithoutArea} usuario(s)");
        }
    }
}
