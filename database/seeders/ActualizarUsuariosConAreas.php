<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Area;

class ActualizarUsuariosConAreas extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('🔄 Actualizando usuarios existentes con áreas reales...');

        // Actualizar administrador
        $this->updateAdmin();

        // Actualizar técnicos
        $this->updateTechnicians();

        // Actualizar usuarios finales
        $this->updateEndUsers();

        $this->command->info('✅ Usuarios actualizados exitosamente!');
        $this->showSummary();
    }

    private function updateAdmin()
    {
        $this->command->info('👨‍💼 Actualizando Administrador...');

        $admin = User::where('email', 'admin@gestion.com')->first();
        if ($admin) {
            $admin->update([
                'area_id' => 8 // GERENCIA MUNICIPAL
            ]);
            $area = Area::find(8);
            $this->command->line("✅ Admin actualizado: {$admin->name} - Área: {$area->nombre}");
        }
    }

    private function updateTechnicians()
    {
        $this->command->info('👨‍🔧 Actualizando Técnicos...');

        $technicianUpdates = [
            'juan.perez@gestion.com' => 18,     // OFICINA DE TECNOLOGÍAS DE LA INFORMACIÓN
            'maria.garcia@gestion.com' => 18,   // OFICINA DE TECNOLOGÍAS DE LA INFORMACIÓN
            'carlos.rodriguez@gestion.com' => 19, // OFICINA DE GESTIÓN DE RECURSOS HUMANOS (Mantenimiento)
            'ana.torres@gestion.com' => 32,     // Área de Seguridad Ciudadana y Serenazgo
            'pedro.morales@gestion.com' => 47,  // Área de Obras Públicas
        ];

        foreach ($technicianUpdates as $email => $areaId) {
            $technician = User::where('email', $email)->first();
            if ($technician) {
                $technician->update(['area_id' => $areaId]);
                $area = Area::find($areaId);
                $this->command->line("✅ Técnico actualizado: {$technician->name} - Área: {$area->nombre}");
            }
        }
    }

    private function updateEndUsers()
    {
        $this->command->info('👥 Actualizando Usuarios finales...');

        // Obtener todos los usuarios con rol Usuario
        $users = User::role('Usuario')->get();

        // Mapeo de usuarios a áreas específicas con IDs correctos
        $userAreaMapping = [
            0 => 22,  // SUB GERENCIA DE RENTAS
            1 => 51,  // Área de Educación, Cultura, Programas Sociales
            2 => 19,  // OFICINA DE GESTIÓN DE RECURSOS HUMANOS
            3 => 14,  // OFICINA DE CONTABILIDAD
            4 => 26,  // SUB GERENCIA DE SERVICIOS PÚBLICOS
            5 => 9,   // OFICINA DE SECRETARÍA GENERAL: ATENCIÓN AL CIUDADANO Y GESTIÓN DOCUMENTARIA
            6 => 23,  // SUB GERENCIA DE FISCALIZACIÓN TRIBUTARIA
            7 => 59,  // SUB GERENCIA DE DESARROLLO ECONÓMICO Y PRODUCTIVO
            8 => 19,  // OFICINA DE GESTIÓN DE RECURSOS HUMANOS
            9 => 14,  // OFICINA DE CONTABILIDAD
            10 => 47, // Área de Obras Públicas
            11 => 15, // OFICINA DE TESORERÍA
            12 => 43, // SUB GERENCIA DE DESARROLLO TERRITORIAL
            13 => 53, // SUB GERENCIA DE PROGRAMAS SOCIALES
            14 => 16, // OFICINA DE ABASTECIMIENTO
            15 => 35, // SUB GERENCIA AMBIENTAL
            16 => 32, // Área de Seguridad Ciudadana y Serenazgo
            17 => 63, // Área de Licencias de Funcionamiento
            18 => 65, // Área de Control Sanitario
            19 => 27, // Área de Registro Civil y Cementerio
        ];

        foreach ($users as $index => $user) {
            if (isset($userAreaMapping[$index])) {
                $areaId = $userAreaMapping[$index];
                $user->update(['area_id' => $areaId]);
                $area = Area::find($areaId);
                $this->command->line("✅ Usuario actualizado: {$user->name} - Área: {$area->nombre}");
            }
        }
    }

    private function showSummary()
    {
        $this->command->info('📊 RESUMEN DE USUARIOS POR ÁREA:');
        $this->command->line('');

        $usersByArea = User::with('area', 'roles')
            ->get()
            ->groupBy('area.nombre');

        foreach ($usersByArea as $areaName => $users) {
            $this->command->line("🏢 {$areaName}:");
            foreach ($users as $user) {
                $roles = $user->roles->pluck('name')->join(', ');
                $this->command->line("  👤 {$user->name} ({$user->email}) - Rol: {$roles}");
            }
            $this->command->line('');
        }

        $totalUsers = User::count();
        $usersByRole = User::with('roles')->get()->groupBy(function ($user) {
            return $user->roles->first()?->name ?? 'Sin rol';
        });

        $this->command->table(
            ['Rol', 'Cantidad'],
            $usersByRole->map(function ($users, $role) {
                return [$role, $users->count()];
            })->toArray()
        );

        $this->command->info("Total de usuarios: {$totalUsers}");
    }
}
