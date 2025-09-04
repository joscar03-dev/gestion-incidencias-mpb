<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Categoria;
use Illuminate\Support\Str;

class ItilCategoriasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Categorías ITIL de Incidentes
        $incidentCategories = [
            'Hardware' => 'Problemas relacionados con equipos físicos, servidores, impresoras, dispositivos móviles',
            'Software' => 'Errores de aplicaciones, sistemas operativos, licencias y actualizaciones',
            'Red' => 'Conectividad, VPN, WiFi, DNS, problemas de ancho de banda',
            'Seguridad' => 'Violaciones de seguridad, malware, accesos no autorizados',
            'Derechos de Acceso' => 'Permisos de usuario, autenticación, autorización',
            'Datos' => 'Pérdida de datos, corrupción, problemas de backup',
            'Disponibilidad del Servicio' => 'Servicios no disponibles, tiempo de inactividad',
            'Rendimiento' => 'Lentitud del sistema, problemas de performance'
        ];

        // Categorías ITIL de Solicitudes de Servicio
        $serviceRequestCategories = [
            'Solicitud de Acceso' => 'Nuevos accesos a sistemas, aplicaciones o recursos',
            'Restablecimiento de Contraseña' => 'Reset de contraseñas y desbloqueo de cuentas',
            'Configuración de Nuevo Usuario' => 'Setup completo para empleados nuevos',
            'Instalación de Software' => 'Instalación y configuración de aplicaciones',
            'Solicitud de Hardware' => 'Equipos nuevos, reemplazos, actualizaciones',
            'Solicitud de Información' => 'Consultas técnicas, documentación, reportes',
            'Solicitud de Servicio' => 'Servicios generales de TI y soporte',
            'Solicitud de Capacitación' => 'Entrenamiento en sistemas y herramientas'
        ];

        // Categorías ITIL de Cambios
        $changeCategories = [
            'Cambio Normal' => 'Cambios planificados que requieren aprobación completa',
            'Cambio Estándar' => 'Cambios preaprobados de bajo riesgo',
            'Cambio de Emergencia' => 'Cambios urgentes para resolver incidentes críticos',
            'Mantenimiento Programado' => 'Mantenimientos preventivos y actualizaciones'
        ];

        // Categorías ITIL de Problemas
        $problemCategories = [
            'Problema Conocido' => 'Problemas con causa raíz identificada',
            'Problema en Investigación' => 'Problemas bajo análisis',
            'Problema Resuelto' => 'Problemas con solución implementada',
            'Problema Recurrente' => 'Problemas que se repiten frecuentemente'
        ];

        // Insertar categorías de incidentes
        foreach ($incidentCategories as $nombre => $descripcion) {
            Categoria::firstOrCreate(
                ['slug' => Str::slug($nombre)],
                [
                    'nombre' => $nombre,
                    'descripcion' => $descripcion,
                    'tipo_categoria' => 'incidente',
                    'itil_category' => true,
                    'prioridad_default' => $this->getPrioridadDefault($nombre),
                    'is_active' => true,
                ]
            );
        }

        // Insertar categorías de solicitudes de servicio
        foreach ($serviceRequestCategories as $nombre => $descripcion) {
            Categoria::firstOrCreate(
                ['slug' => Str::slug($nombre)],
                [
                    'nombre' => $nombre,
                    'descripcion' => $descripcion,
                    'tipo_categoria' => 'solicitud_servicio',
                    'itil_category' => true,
                    'prioridad_default' => 'media',
                    'is_active' => true,
                ]
            );
        }

        // Insertar categorías de cambios
        foreach ($changeCategories as $nombre => $descripcion) {
            Categoria::firstOrCreate(
                ['slug' => Str::slug($nombre)],
                [
                    'nombre' => $nombre,
                    'descripcion' => $descripcion,
                    'tipo_categoria' => 'cambio',
                    'itil_category' => true,
                    'prioridad_default' => $this->getPrioridadCambio($nombre),
                    'is_active' => true,
                ]
            );
        }

        // Insertar categorías de problemas
        foreach ($problemCategories as $nombre => $descripcion) {
            Categoria::firstOrCreate(
                ['slug' => Str::slug($nombre)],
                [
                    'nombre' => $nombre,
                    'descripcion' => $descripcion,
                    'tipo_categoria' => 'problema',
                    'itil_category' => true,
                    'prioridad_default' => 'alta',
                    'is_active' => true,
                ]
            );
        }

        $this->command->info('✅ Categorías ITIL creadas exitosamente!');
        $this->command->info('📊 Total categorías: ' . Categoria::where('itil_category', true)->count());
    }

    /**
     * Obtiene la prioridad por defecto según la categoría de incidente
     */
    private function getPrioridadDefault($categoria): string
    {
        $prioridades = [
            'Seguridad' => 'critica',
            'Disponibilidad del Servicio' => 'alta',
            'Hardware' => 'alta',
            'Red' => 'alta',
            'Software' => 'media',
            'Rendimiento' => 'media',
            'Derechos de Acceso' => 'media',
            'Datos' => 'alta'
        ];

        return $prioridades[$categoria] ?? 'media';
    }

    /**
     * Obtiene la prioridad por defecto para cambios
     */
    private function getPrioridadCambio($categoria): string
    {
        $prioridades = [
            'Cambio de Emergencia' => 'critica',
            'Cambio Normal' => 'media',
            'Cambio Estándar' => 'baja',
            'Mantenimiento Programado' => 'media'
        ];

        return $prioridades[$categoria] ?? 'media';
    }
}
