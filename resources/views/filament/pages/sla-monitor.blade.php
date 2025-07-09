<x-filament-panels::page>
    <div class="space-y-6">
        <!-- Información del Sistema -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                <h3 class="text-lg font-semibold text-blue-800 mb-2">🎯 Sistema Híbrido</h3>
                <p class="text-sm text-blue-600">
                    Combina SLA por área con prioridad de ticket para garantizar atención adecuada.
                </p>
            </div>

            <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                <h3 class="text-lg font-semibold text-green-800 mb-2">⚡ Escalamiento Automático</h3>
                <p class="text-sm text-green-600">
                    Los tickets se escalan automáticamente cuando superan los tiempos definidos.
                </p>
            </div>

            <div class="bg-purple-50 border border-purple-200 rounded-lg p-4">
                <h3 class="text-lg font-semibold text-purple-800 mb-2">📊 Monitoreo en Tiempo Real</h3>
                <p class="text-sm text-purple-600">
                    Seguimiento continuo del cumplimiento de SLA con alertas automáticas.
                </p>
            </div>
        </div>

        <!-- Tabla de Prioridades -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">💡 Matriz de Prioridades</h3>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Prioridad</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Factor</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Descripción</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Ejemplo (Área IT)</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <tr class="bg-red-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                    🔴 Crítica
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">20%</td>
                            <td class="px-6 py-4 text-sm text-gray-500">Emergencias que afectan la operación</td>
                            <td class="px-6 py-4 text-sm text-gray-500">6 min respuesta / 48 min resolución</td>
                        </tr>
                        <tr class="bg-orange-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-orange-100 text-orange-800">
                                    🟠 Alta
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">50%</td>
                            <td class="px-6 py-4 text-sm text-gray-500">Problemas importantes con impacto significativo</td>
                            <td class="px-6 py-4 text-sm text-gray-500">15 min respuesta / 2h resolución</td>
                        </tr>
                        <tr class="bg-yellow-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                    🟡 Media
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">100%</td>
                            <td class="px-6 py-4 text-sm text-gray-500">Tickets regulares de operación normal</td>
                            <td class="px-6 py-4 text-sm text-gray-500">30 min respuesta / 4h resolución</td>
                        </tr>
                        <tr class="bg-green-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    🟢 Baja
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">150%</td>
                            <td class="px-6 py-4 text-sm text-gray-500">Consultas y mejoras sin urgencia</td>
                            <td class="px-6 py-4 text-sm text-gray-500">45 min respuesta / 6h resolución</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Comandos útiles -->
        <div class="bg-gray-50 rounded-lg p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">⚙️ Comandos de Administración</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="bg-white rounded-lg p-4 border">
                    <h4 class="font-medium text-gray-900 mb-2">Verificación Manual</h4>
                    <code class="text-sm bg-gray-100 p-2 rounded block">php artisan tickets:verificar-sla --sync</code>
                </div>
                <div class="bg-white rounded-lg p-4 border">
                    <h4 class="font-medium text-gray-900 mb-2">Programación Automática</h4>
                    <code class="text-sm bg-gray-100 p-2 rounded block">Cada 5 minutos via cron</code>
                </div>
                <div class="bg-white rounded-lg p-4 border">
                    <h4 class="font-medium text-gray-900 mb-2">Ejecutar Seeders</h4>
                    <code class="text-sm bg-gray-100 p-2 rounded block">php artisan db:seed --class=SlaSeeder</code>
                </div>
                <div class="bg-white rounded-lg p-4 border">
                    <h4 class="font-medium text-gray-900 mb-2">Migrar Base de Datos</h4>
                    <code class="text-sm bg-gray-100 p-2 rounded block">php artisan migrate</code>
                </div>
            </div>
        </div>

        <!-- Widgets de estadísticas -->
        <div>
            @livewire(\App\Filament\Widgets\SlaStatsWidget::class)
        </div>

        <!-- Widget de tickets críticos -->
        <div>
            @livewire(\App\Filament\Widgets\CriticalTicketsWidget::class)
        </div>
    </div>
</x-filament-panels::page>
