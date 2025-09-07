<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Definir los modelos y sus acciones
        $modelos = [
            'area',
            'categoria',
            'categoria-dispositivo',
            'dispositivo',
            'dispositivo-asignacion',
            'solicitud-dispositivo',
            'solicitud-transferencia',
            'sla',
            'factor-prioridad-sla',
            'factor-tipo-sla',
            'monitor-sla',
            'encuesta-satisfaccion',
            'dashboard-itil',
            'ticket',
            'user',
            'role',
            'permission',
            'ticket-administrador',
            'local'
        ];

        $acciones = [
            'ver',
            'crear',
            'editar',
            'borrar'
        ];

        // Crear permisos para cada modelo y acción
        foreach ($modelos as $modelo) {
            foreach ($acciones as $accion) {
                $nombrePermiso = "{$accion}-{$modelo}";

                Permission::firstOrCreate([
                    'name' => $nombrePermiso,
                    'guard_name' => 'web'
                ]);

                $this->command->info("Permiso creado: {$nombrePermiso}");
            }
        }

        // Crear roles básicos si no existen
        $roles = [
            'Super Admin' => 'Acceso completo al sistema',
            'Admin' => 'Administrador del sistema',
            'Jefe de Administración' => 'Jefe de área administrativa',
            'Técnico' => 'Personal técnico',
            'Usuario' => 'Usuario final'
        ];

        foreach ($roles as $nombreRol => $descripcion) {
            $rol = Role::firstOrCreate([
                'name' => $nombreRol,
                'guard_name' => 'web'
            ]);

            $this->command->info("Rol creado: {$nombreRol}");
        }

        // Asignar todos los permisos al Super Admin
        $superAdmin = Role::where('name', 'Super Admin')->first();
        if ($superAdmin) {
            $todosLosPermisos = Permission::all();
            $superAdmin->syncPermissions($todosLosPermisos);
            $this->command->info("Todos los permisos asignados al Super Admin");
        }

        // Asignar permisos específicos al Admin
        $admin = Role::where('name', 'Admin')->first();
        if ($admin) {
            $permisosAdmin = Permission::whereIn('name', [
                'ver-area',
                'crear-area',
                'editar-area',
                'borrar-area',
                'ver-categoria',
                'crear-categoria',
                'editar-categoria',
                'borrar-categoria',
                'ver-categoria-dispositivo',
                'crear-categoria-dispositivo',
                'editar-categoria-dispositivo',
                'borrar-categoria-dispositivo',
                'ver-dispositivo',
                'crear-dispositivo',
                'editar-dispositivo',
                'borrar-dispositivo',
                'ver-dispositivo-asignacion',
                'crear-dispositivo-asignacion',
                'editar-dispositivo-asignacion',
                'borrar-dispositivo-asignacion',
                'ver-sla',
                'crear-sla',
                'editar-sla',
                'borrar-sla',
                'ver-factor-prioridad-sla',
                'crear-factor-prioridad-sla',
                'editar-factor-prioridad-sla',
                'borrar-factor-prioridad-sla',
                'ver-factor-tipo-sla',
                'crear-factor-tipo-sla',
                'editar-factor-tipo-sla',
                'borrar-factor-tipo-sla',
                'ver-monitor-sla',
                'crear-monitor-sla',
                'editar-monitor-sla',
                'borrar-monitor-sla',
                'ver-encuesta-satisfaccion',
                'crear-encuesta-satisfaccion',
                'editar-encuesta-satisfaccion',
                'borrar-encuesta-satisfaccion',
                'ver-dashboard-itil',
                'crear-dashboard-itil',
                'editar-dashboard-itil',
                'borrar-dashboard-itil',
                'ver-ticket',
                'crear-ticket',
                'editar-ticket',
                'borrar-ticket',
                'ver-user',
                'crear-user',
                'editar-user',
                'ver-local',
                'editar-local',
                'borrar-local',
                'crear-local'
            ])->get();
            $admin->syncPermissions($permisosAdmin);
            $this->command->info("Permisos específicos asignados al Admin");
        }

        // Asignar permisos específicos al Técnico (RESTRINGIDOS)
        $tecnico = Role::where('name', 'Técnico')->first();
        if ($tecnico) {
            $permisosTecnico = Permission::whereIn('name', [
                'ver-categoria',
                'ver-sla',
                'ver-categoria-dispositivo',
                'ver-dispositivo',
                'crear-dispositivo',
                'editar-dispositivo',
                'ver-dispositivo-asignacion',
                'crear-dispositivo-asignacion',
                'editar-dispositivo-asignacion',
                'ver-ticket',
                'crear-ticket',
                'editar-ticket',
            ])->get();
            $tecnico->syncPermissions($permisosTecnico);
            $this->command->info("Permisos específicos asignados al Técnico (sin acceso a SLA, factores, monitor, encuestas ni dashboard ITIL)");
        }

        // Asignar permisos específicos al Jefe de Administración
        $jefeAdmin = Role::where('name', 'Jefe de Administración')->first();
        if ($jefeAdmin) {
            $permisosJefeAdmin = Permission::whereIn('name', [
                // Gestión de solicitudes y transferencias
                'ver-solicitud-dispositivo',
                'crear-solicitud-dispositivo',
                'editar-solicitud-dispositivo',
                'borrar-solicitud-dispositivo',
                'ver-solicitud-transferencia',
                'crear-solicitud-transferencia',
                'editar-solicitud-transferencia',
                'borrar-solicitud-transferencia',
                // Tickets
                'ver-ticket',
                'crear-ticket',
                'editar-ticket',
                // Consulta de dispositivos y categorías
                'ver-categoria-dispositivo',
                'ver-dispositivo',
                'ver-dispositivo-asignacion',
                // Consulta de áreas y categorías
                'ver-area',
                'ver-categoria',
            ])->get();
            $jefeAdmin->syncPermissions($permisosJefeAdmin);
            $this->command->info("Permisos específicos asignados al Jefe de Administración (solicitudes de dispositivos, transferencias y tickets)");
        }

        // Asignar permisos específicos al Usuario
        $usuario = Role::where('name', 'Usuario')->first();
        if ($usuario) {
            $permisosUsuario = Permission::whereIn('name', [
                'ver-area',
                'ver-categoria',
                'ver-dispositivo',
                'ver-ticket',
                'crear-ticket'
            ])->get();
            $usuario->syncPermissions($permisosUsuario);
            $this->command->info("Permisos específicos asignados al Usuario");
        }

        // Crear usuario Super Admin si no existe
        $superAdminUser = User::where('email', 'superadmin@superadmin.com')->first();

        if (!$superAdminUser) {
            // Crear área de administración para el Super Admin
            $areaAdmin = \App\Models\Area::firstOrCreate([
                'nombre' => 'Administración'
            ], [
                'descripcion' => 'Área de administración del sistema'
            ]);

            $superAdminUser = User::create([
                'name' => 'Super Administrador',
                'email' => 'superadmin@superadmin.com',
                'password' => Hash::make('verano8080'),
                'email_verified_at' => now(),
                'area_id' => $areaAdmin->id,
            ]);

            $this->command->info("Usuario Super Admin creado: superadmin@superadmin.com");
        }

        // Asignar el rol Super Admin al usuario
        $superAdminRole = Role::where('name', 'Super Admin')->first();
        if ($superAdminRole && $superAdminUser) {
            $superAdminUser->assignRole($superAdminRole);
            $this->command->info("Rol 'Super Admin' asignado al usuario: {$superAdminUser->email}");
        }

        $this->command->info('Seeder de permisos completado exitosamente!');
    }
}
