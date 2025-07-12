<x-filament-panels::page>
    <div class="space-y-6">
        <!-- Header del catálogo -->
        <div class="bg-gradient-to-r from-blue-600 to-indigo-600 rounded-lg shadow-lg p-8 text-white">
            <h1 class="text-3xl font-bold mb-2">Catálogo de Servicios ITIL</h1>
            <p class="text-blue-100">Marco completo de servicios de TI siguiendo las mejores prácticas de ITIL v4</p>
        </div>

        <!-- Categorías principales de servicios -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            @php
                $serviceCategories = $this->getViewData()['service_categories'];
            @endphp
            @foreach ($serviceCategories as $key => $category)
                <div class="bg-white rounded-lg shadow p-6 hover:shadow-lg transition-shadow duration-200">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-gray-900">{{ $category }}</h3>
                        <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                            <svg class="w-4 h-4 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                    </div>
                    <p class="text-sm text-gray-600">
                        @switch($key)
                            @case('incident_management')
                                Gestión y resolución de interrupciones no planificadas en los servicios de TI.
                                @break
                            @case('service_request')
                                Procesamiento de solicitudes de servicios estándar de los usuarios.
                                @break
                            @case('change_management')
                                Control y planificación de cambios en la infraestructura de TI.
                                @break
                            @case('problem_management')
                                Identificación y resolución de causas raíz de incidentes recurrentes.
                                @break
                            @case('configuration_management')
                                Gestión de elementos de configuración y sus relaciones.
                                @break
                            @case('release_management')
                                Planificación y despliegue de versiones de software y hardware.
                                @break
                            @case('knowledge_management')
                                Captura, almacenamiento y distribución del conocimiento organizacional.
                                @break
                            @case('service_level_management')
                                Definición, monitoreo y mejora de niveles de servicio acordados.
                                @break
                        @endswitch
                    </p>
                </div>
            @endforeach
        </div>

        <!-- Matriz de prioridades ITIL -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-xl font-semibold text-gray-900 mb-6">Matriz de Prioridades ITIL</h3>
            @php
                $priorityMatrix = $this->getViewData()['priority_matrix'];
            @endphp
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Prioridad
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Impacto
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Urgencia
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                SLA (Horas)
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Descripción
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach ($priorityMatrix as $key => $priority)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium
                                        {{ $key === 'critical' ? 'bg-red-100 text-red-800' : 
                                           ($key === 'high' ? 'bg-orange-100 text-orange-800' : 
                                           ($key === 'medium' ? 'bg-yellow-100 text-yellow-800' : 'bg-green-100 text-green-800')) }}">
                                        {{ ucfirst($key) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $priority['impact'] }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $priority['urgency'] }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        {{ $priority['sla_hours'] }}h
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-900">
                                    @switch($key)
                                        @case('critical')
                                            Servicio completamente interrumpido, afecta a toda la organización
                                            @break
                                        @case('high')
                                            Servicio significativamente degradado, afecta a múltiples usuarios
                                            @break
                                        @case('medium')
                                            Servicio parcialmente interrumpido, afecta a algunos usuarios
                                            @break
                                        @case('low')
                                            Interrupción menor o solicitud de información
                                            @break
                                    @endswitch
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Categorías de incidentes -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-xl font-semibold text-gray-900 mb-4">Categorías de Incidentes ITIL</h3>
                @php
                    $incidentCategories = $this->getViewData()['incident_categories'];
                @endphp
                <div class="space-y-3">
                    @foreach ($incidentCategories as $key => $category)
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                            <div class="flex items-center">
                                <div class="w-3 h-3 bg-blue-500 rounded-full mr-3"></div>
                                <span class="text-sm font-medium text-gray-900">{{ $category }}</span>
                            </div>
                            <span class="text-xs text-gray-500">{{ $key }}</span>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-xl font-semibold text-gray-900 mb-4">Categorías de Solicitudes de Servicio</h3>
                @php
                    $serviceRequestCategories = $this->getViewData()['service_request_categories'];
                @endphp
                <div class="space-y-3">
                    @foreach ($serviceRequestCategories as $key => $category)
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                            <div class="flex items-center">
                                <div class="w-3 h-3 bg-green-500 rounded-full mr-3"></div>
                                <span class="text-sm font-medium text-gray-900">{{ $category }}</span>
                            </div>
                            <span class="text-xs text-gray-500">{{ $key }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Niveles de servicio y tipos de cambio -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-xl font-semibold text-gray-900 mb-4">Niveles de Servicio ITIL</h3>
                @php
                    $serviceLevels = $this->getViewData()['service_levels'];
                @endphp
                <div class="space-y-4">
                    @foreach ($serviceLevels as $key => $level)
                        <div class="border rounded-lg p-4 
                            {{ $key === 'gold' ? 'border-yellow-300 bg-yellow-50' : 
                               ($key === 'silver' ? 'border-gray-300 bg-gray-50' : 'border-orange-300 bg-orange-50') }}">
                            <div class="flex items-center justify-between mb-2">
                                <h4 class="text-lg font-semibold 
                                    {{ $key === 'gold' ? 'text-yellow-800' : 
                                       ($key === 'silver' ? 'text-gray-800' : 'text-orange-800') }}">
                                    {{ $level['name'] }}
                                </h4>
                                <span class="text-xs font-medium px-2 py-1 rounded 
                                    {{ $key === 'gold' ? 'bg-yellow-200 text-yellow-800' : 
                                       ($key === 'silver' ? 'bg-gray-200 text-gray-800' : 'bg-orange-200 text-orange-800') }}">
                                    {{ ucfirst($key) }}
                                </span>
                            </div>
                            <div class="grid grid-cols-2 gap-4 text-sm">
                                <div>
                                    <span class="text-gray-600">Tiempo Respuesta:</span>
                                    <span class="font-medium">{{ $level['response_time'] }} min</span>
                                </div>
                                <div>
                                    <span class="text-gray-600">Tiempo Resolución:</span>
                                    <span class="font-medium">{{ $level['resolution_time'] }} horas</span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-xl font-semibold text-gray-900 mb-4">Tipos de Cambios ITIL</h3>
                @php
                    $changeTypes = $this->getViewData()['change_types'];
                @endphp
                <div class="space-y-4">
                    @foreach ($changeTypes as $key => $type)
                        <div class="border rounded-lg p-4">
                            <h4 class="text-lg font-semibold text-gray-900 mb-2">{{ $type }}</h4>
                            <p class="text-sm text-gray-600">
                                @switch($key)
                                    @case('normal')
                                        Cambios que requieren planificación y aprobación del CAB (Change Advisory Board).
                                        @break
                                    @case('standard')
                                        Cambios preaprobados, de bajo riesgo y bien documentados.
                                        @break
                                    @case('emergency')
                                        Cambios urgentes para resolver incidentes críticos o implementar parches de seguridad.
                                        @break
                                    @case('maintenance')
                                        Actividades de mantenimiento programado y rutinario del sistema.
                                        @break
                                @endswitch
                            </p>
                            <div class="mt-2">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                    {{ $key === 'emergency' ? 'bg-red-100 text-red-800' : 
                                       ($key === 'normal' ? 'bg-blue-100 text-blue-800' : 
                                       ($key === 'standard' ? 'bg-green-100 text-green-800' : 'bg-purple-100 text-purple-800')) }}">
                                    {{ ucfirst($key) }}
                                </span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Footer informativo -->
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-6">
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    <svg class="w-6 h-6 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-blue-800">Información sobre ITIL</h3>
                    <div class="mt-2 text-sm text-blue-700">
                        <p>Este catálogo sigue las mejores prácticas de ITIL v4 (Information Technology Infrastructure Library), 
                        proporcionando un marco completo para la gestión de servicios de TI que ayuda a las organizaciones 
                        a alinear sus servicios de TI con las necesidades del negocio.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-filament-panels::page>
