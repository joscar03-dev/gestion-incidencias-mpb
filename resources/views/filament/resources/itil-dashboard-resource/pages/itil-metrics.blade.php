<x-filament-panels::page>
    <div class="space-y-6">
        <!-- Header con estadísticas clave -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            @php
                $metrics = $this->getViewData()['incident_metrics'];
            @endphp

            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="flex-1">
                        <h3 class="text-lg font-semibold text-gray-900">Total Incidentes</h3>
                        <p class="text-3xl font-bold text-blue-600">{{ $metrics['total_incidents'] }}</p>
                    </div>
                    <div class="ml-4">
                        <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                            <svg class="w-4 h-4 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="flex-1">
                        <h3 class="text-lg font-semibold text-gray-900">Cumplimiento SLA</h3>
                        <p class="text-3xl font-bold text-green-600">{{ $metrics['sla_compliance'] }}%</p>
                    </div>
                    <div class="ml-4">
                        <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                            <svg class="w-4 h-4 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"/>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="flex-1">
                        <h3 class="text-lg font-semibold text-gray-900">Tasa Resolución</h3>
                        <p class="text-3xl font-bold text-purple-600">{{ $metrics['resolution_rate'] }}%</p>
                    </div>
                    <div class="ml-4">
                        <div class="w-8 h-8 bg-purple-100 rounded-full flex items-center justify-center">
                            <svg class="w-4 h-4 text-purple-600" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3z"/>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="flex-1">
                        <h3 class="text-lg font-semibold text-gray-900">Disponibilidad</h3>
                        @php
                            $availability = $this->getViewData()['service_availability'];
                        @endphp
                        <p class="text-3xl font-bold text-indigo-600">{{ $availability['availability_percentage'] }}%</p>
                    </div>
                    <div class="ml-4">
                        <div class="w-8 h-8 bg-indigo-100 rounded-full flex items-center justify-center">
                            <svg class="w-4 h-4 text-indigo-600" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zM3 10a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H4a1 1 0 01-1-1v-6zM14 9a1 1 0 00-1 1v6a1 1 0 001 1h2a1 1 0 001-1v-6a1 1 0 00-1-1h-2z"/>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Widgets de gráficos ApexCharts -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            @foreach ($this->getHeaderWidgets() as $widget)
                @livewire($widget)
            @endforeach
        </div>

        <!-- Gráficos adicionales simples -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Métricas de Incidentes -->
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Distribución de Incidentes</h3>
                @php
                    $metrics = $this->getViewData()['incident_metrics'];
                    $total = $metrics['total_incidents'];
                @endphp
                <div class="space-y-4">
                    <div class="flex items-center justify-between">
                        <span class="text-gray-600">Abiertos</span>
                        <div class="flex items-center">
                            <div class="w-32 bg-gray-200 rounded-full h-2 mr-2">
                                <div class="bg-yellow-500 h-2 rounded-full" style="width: {{ $total > 0 ? ($metrics['open_incidents'] / $total) * 100 : 0 }}%"></div>
                            </div>
                            <span class="font-semibold">{{ $metrics['open_incidents'] }}</span>
                        </div>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-gray-600">Resueltos</span>
                        <div class="flex items-center">
                            <div class="w-32 bg-gray-200 rounded-full h-2 mr-2">
                                <div class="bg-green-500 h-2 rounded-full" style="width: {{ $total > 0 ? ($metrics['resolved_incidents'] / $total) * 100 : 0 }}%"></div>
                            </div>
                            <span class="font-semibold">{{ $metrics['resolved_incidents'] }}</span>
                        </div>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-gray-600">Escalados</span>
                        <div class="flex items-center">
                            <div class="w-32 bg-gray-200 rounded-full h-2 mr-2">
                                <div class="bg-red-500 h-2 rounded-full" style="width: {{ $total > 0 ? ($metrics['escalated_incidents'] / $total) * 100 : 0 }}%"></div>
                            </div>
                            <span class="font-semibold">{{ $metrics['escalated_incidents'] }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Cumplimiento SLA -->
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Cumplimiento SLA</h3>
                <div class="text-center">
                    <div class="relative w-32 h-32 mx-auto">
                        <svg class="w-32 h-32 transform -rotate-90" viewBox="0 0 36 36">
                            <path class="text-gray-300" stroke="currentColor" stroke-width="3" fill="none" d="M18 2.0845
                              a 15.9155 15.9155 0 0 1 0 31.831
                              a 15.9155 15.9155 0 0 1 0 -31.831" />
                            <path class="{{ $metrics['sla_compliance'] >= 90 ? 'text-green-500' : 'text-red-500' }}"
                                  stroke="currentColor" stroke-width="3" fill="none" stroke-linecap="round"
                                  stroke-dasharray="{{ $metrics['sla_compliance'] }}, 100"
                                  d="M18 2.0845
                                    a 15.9155 15.9155 0 0 1 0 31.831
                                    a 15.9155 15.9155 0 0 1 0 -31.831" />
                        </svg>
                        <div class="absolute inset-0 flex items-center justify-center">
                            <span class="text-2xl font-bold {{ $metrics['sla_compliance'] >= 90 ? 'text-green-600' : 'text-red-600' }}">
                                {{ $metrics['sla_compliance'] }}%
                            </span>
                        </div>
                    </div>
                    <p class="mt-4 text-gray-600">
                        {{ $metrics['sla_compliance'] >= 95 ? 'Excelente' : ($metrics['sla_compliance'] >= 90 ? 'Bueno' : 'Requiere Atención') }}
                    </p>
                </div>
            </div>
        </div>

        <!-- Métricas detalladas -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Análisis de tiempo de resolución -->
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Métricas de Tiempo de Resolución</h3>
                @php
                    $resolutionMetrics = $this->getViewData()['resolution_metrics'];
                @endphp
                <div class="space-y-4">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Tiempo Promedio:</span>
                        <span class="font-semibold">{{ round($resolutionMetrics['mean_time_to_resolve'] ?? 0, 2) }} horas</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Tiempo Mediano:</span>
                        <span class="font-semibold">{{ round($resolutionMetrics['median_time_to_resolve'] ?? 0, 2) }} horas</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Tiempo Mínimo:</span>
                        <span class="font-semibold">{{ round($resolutionMetrics['min_time_to_resolve'] ?? 0, 2) }} horas</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Tiempo Máximo:</span>
                        <span class="font-semibold">{{ round($resolutionMetrics['max_time_to_resolve'] ?? 0, 2) }} horas</span>
                    </div>
                </div>
            </div>

            <!-- Análisis de carga de trabajo -->
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Carga de Trabajo del Equipo</h3>
                @php
                    $workload = $this->getViewData()['workload_analysis'];
                @endphp
                <div class="space-y-3">
                    @foreach (array_slice($workload, 0, 5) as $analyst)
                        <div class="flex items-center justify-between">
                            <div class="flex-1">
                                <div class="text-sm font-medium text-gray-900">{{ $analyst['user_name'] }}</div>
                                <div class="text-xs text-gray-500">{{ $analyst['open_tickets'] }} tickets abiertos</div>
                            </div>
                            <div class="ml-4">
                                <div class="text-sm font-medium text-gray-900">{{ $analyst['resolution_rate'] }}%</div>
                                <div class="w-20 bg-gray-200 rounded-full h-2">
                                    <div class="bg-blue-600 h-2 rounded-full" style="width: {{ $analyst['resolution_rate'] }}%"></div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Análisis de satisfacción del usuario -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Métricas de Satisfacción del Usuario</h3>
            @php
                $satisfaction = $this->getViewData()['user_satisfaction'];
            @endphp
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div class="text-center">
                    <div class="text-2xl font-bold text-blue-600">{{ $satisfaction['satisfaction_score'] }}%</div>
                    <div class="text-sm text-gray-600">Puntuación General</div>
                </div>
                <div class="text-center">
                    <div class="text-2xl font-bold text-green-600">{{ $satisfaction['total_surveys'] }}</div>
                    <div class="text-sm text-gray-600">Total Encuestas</div>
                </div>
                <div class="text-center">
                    <div class="text-2xl font-bold text-purple-600">{{ $satisfaction['response_rate'] }}%</div>
                    <div class="text-sm text-gray-600">Tasa Respuesta</div>
                </div>
                <div class="text-center">
                    <div class="text-2xl font-bold text-indigo-600">{{ $satisfaction['net_promoter_score'] }}</div>
                    <div class="text-sm text-gray-600">Net Promoter Score</div>
                </div>
            </div>
        </div>
    </div>
</x-filament-panels::page>
