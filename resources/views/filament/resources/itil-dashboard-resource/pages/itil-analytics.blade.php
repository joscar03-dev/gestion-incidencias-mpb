<x-filament-panels::page>
    @php
        $efficiency = $this->getViewData()['efficiency_indicators'];
        $quality = $this->getViewData()['quality_indicators'];
        $comparison = $this->getViewData()['performance_comparison'];
        $lastUpdated = $this->getViewData()['last_updated'];
    @endphp

    <div class="space-y-6">
        <!-- Header con indicadores principales -->
        <div class="bg-gradient-to-r from-blue-600 to-blue-700 rounded-lg shadow-lg p-6 text-white">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-2xl font-bold">Indicadores de Eficiencia y Calidad ITIL</h2>
                <div class="text-sm opacity-90">
                    Última actualización: {{ $lastUpdated }}
                </div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div class="bg-white/10 rounded-lg p-4">
                    <div class="text-lg font-bold">{{ $efficiency['overall_efficiency'] }}%</div>
                    <div class="text-sm opacity-90">Eficiencia General</div>
                    <div class="text-xs mt-1">
                        @if($comparison['efficiency_trend'] > 0)
                            <span class="text-green-200">↗ +{{ number_format($comparison['efficiency_trend'], 1) }}%</span>
                        @elseif($comparison['efficiency_trend'] < 0)
                            <span class="text-red-200">↘ {{ number_format($comparison['efficiency_trend'], 1) }}%</span>
                        @else
                            <span class="text-gray-200">→ Sin cambios</span>
                        @endif
                    </div>
                </div>
                <div class="bg-white/10 rounded-lg p-4">
                    <div class="text-lg font-bold">{{ $quality['overall_quality_score'] }}%</div>
                    <div class="text-sm opacity-90">Calidad General</div>
                    <div class="text-xs mt-1">{{ $comparison['performance_indicators']['quality_status'] }}</div>
                </div>
                <div class="bg-white/10 rounded-lg p-4">
                    <div class="text-lg font-bold">{{ $efficiency['avg_resolution_time'] }}h</div>
                    <div class="text-sm opacity-90">Tiempo Promedio</div>
                    <div class="text-xs mt-1">{{ $efficiency['resolution_velocity'] }} tickets/día</div>
                </div>
                <div class="bg-white/10 rounded-lg p-4">
                    <div class="text-lg font-bold">{{ $quality['sla_compliance_rate'] }}%</div>
                    <div class="text-sm opacity-90">Cumplimiento SLA</div>
                    <div class="text-xs mt-1">FCR: {{ $quality['first_call_resolution_rate'] }}%</div>
                </div>
            </div>
        </div>

        <!-- Indicadores de Eficiencia -->
        <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">
            <!-- Productividad por Técnico -->
            <div class="bg-white rounded-lg shadow-lg p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900">Productividad por Técnico</h3>
                    <div class="text-sm text-gray-500">Top 10</div>
                </div>
                <div class="space-y-3">
                    @foreach($efficiency['technician_productivity']->take(10) as $tech)
                        <div class="border rounded-lg p-3">
                            <div class="flex items-center justify-between mb-2">
                                <div class="font-medium text-gray-900">{{ $tech['technician'] }}</div>
                                <div class="flex items-center space-x-2">
                                    <span class="text-sm font-bold
                                        @if($tech['efficiency_rate'] >= 80) text-green-600
                                        @elseif($tech['efficiency_rate'] >= 60) text-yellow-600
                                        @else text-red-600 @endif">
                                        {{ $tech['efficiency_rate'] }}%
                                    </span>
                                    <span class="text-xs px-2 py-1 rounded-full
                                        @if($tech['workload_status'] == 'Alto') bg-red-100 text-red-800
                                        @elseif($tech['workload_status'] == 'Medio') bg-yellow-100 text-yellow-800
                                        @else bg-green-100 text-green-800 @endif">
                                        {{ $tech['workload_status'] }}
                                    </span>
                                </div>
                            </div>
                            <div class="flex justify-between text-sm text-gray-600">
                                <span>Asignados: {{ $tech['total_assigned'] }}</span>
                                <span>Resueltos: {{ $tech['resolved'] }}</span>
                                <span>Pendientes: {{ $tech['pending'] }}</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2 mt-2">
                                <div class="bg-blue-600 h-2 rounded-full" style="width: {{ $tech['efficiency_rate'] }}%"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Eficiencia por Categoría -->
            <div class="bg-white rounded-lg shadow-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Eficiencia por Categoría ITIL</h3>
                <div class="space-y-3">
                    @foreach($efficiency['category_efficiency']->take(8) as $category)
                        <div class="border rounded-lg p-3">
                            <div class="flex items-center justify-between mb-2">
                                <div class="font-medium text-gray-900">{{ $category['category'] }}</div>
                                <div class="text-sm font-bold text-blue-600">
                                    Score: {{ $category['efficiency_score'] }}
                                </div>
                            </div>
                            <div class="grid grid-cols-3 gap-4 text-xs text-gray-600">
                                <div>Total: {{ $category['total_tickets'] }}</div>
                                <div>Resueltos: {{ $category['resolved_tickets'] }}</div>
                                <div>Tiempo: {{ $category['avg_resolution_time'] }}h</div>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2 mt-2">
                                <div class="bg-green-600 h-2 rounded-full" style="width: {{ $category['resolution_rate'] }}%"></div>
                            </div>
                            <div class="text-xs text-gray-500 mt-1">{{ $category['resolution_rate'] }}% resueltos</div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Indicadores de Calidad -->
        <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">
            <!-- Calidad por Técnico -->
            <div class="bg-white rounded-lg shadow-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Calidad por Técnico</h3>
                <div class="space-y-3">
                    @foreach($quality['technician_quality']->take(10) as $tech)
                        <div class="border rounded-lg p-3">
                            <div class="flex items-center justify-between mb-2">
                                <div class="font-medium text-gray-900">{{ $tech['technician'] }}</div>
                                <div class="text-sm font-bold
                                    @if($tech['quality_score'] >= 80) text-green-600
                                    @elseif($tech['quality_score'] >= 60) text-yellow-600
                                    @else text-red-600 @endif">
                                    {{ $tech['quality_score'] }}/100
                                </div>
                            </div>
                            <div class="grid grid-cols-2 gap-4 text-xs text-gray-600">
                                <div>Satisfacción: {{ $tech['avg_satisfaction'] }}/5</div>
                                <div>Tickets: {{ $tech['resolved_tickets'] }}</div>
                                <div>Reaperturas: {{ $tech['reopen_rate'] }}%</div>
                                <div>Escalaciones: {{ $tech['escalation_rate'] }}%</div>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2 mt-2">
                                <div class="
                                    @if($tech['quality_score'] >= 80) bg-green-600
                                    @elseif($tech['quality_score'] >= 60) bg-yellow-600
                                    @else bg-red-600 @endif
                                    h-2 rounded-full" style="width: {{ $tech['quality_score'] }}%"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Análisis de Escalaciones -->
            <div class="bg-white rounded-lg shadow-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Análisis de Escalaciones</h3>
                <div class="bg-gray-50 rounded-lg p-4 mb-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div class="text-center">
                            <div class="text-2xl font-bold text-red-600">{{ $quality['escalation_analysis']['total_escalated'] }}</div>
                            <div class="text-sm text-gray-600">Total Escalados</div>
                        </div>
                        <div class="text-center">
                            <div class="text-2xl font-bold text-red-600">{{ $quality['escalation_analysis']['escalation_rate'] }}%</div>
                            <div class="text-sm text-gray-600">Tasa de Escalación</div>
                        </div>
                    </div>
                </div>
                <h4 class="font-medium text-gray-900 mb-3">Escalaciones por Categoría</h4>
                <div class="space-y-2">
                    @foreach($quality['escalation_analysis']['escalation_by_category'] as $escalation)
                        <div class="flex items-center justify-between p-2 bg-gray-50 rounded">
                            <div class="text-sm text-gray-900">{{ $escalation['category'] }}</div>
                            <div class="flex items-center space-x-2">
                                <span class="text-xs text-gray-600">{{ $escalation['escalated'] }}/{{ $escalation['total'] }}</span>
                                <span class="text-sm font-medium
                                    @if($escalation['escalation_rate'] > 15) text-red-600
                                    @elseif($escalation['escalation_rate'] > 8) text-yellow-600
                                    @else text-green-600 @endif">
                                    {{ $escalation['escalation_rate'] }}%
                                </span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Satisfacción por Categoría -->
        <div class="bg-white rounded-lg shadow-lg p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Satisfacción del Cliente por Categoría</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                @foreach($quality['satisfaction_by_category']->take(8) as $satisfaction)
                    <div class="border rounded-lg p-4 text-center">
                        <div class="text-sm font-medium text-gray-900 mb-2">{{ $satisfaction['category'] }}</div>
                        <div class="text-2xl font-bold
                            @if($satisfaction['quality_score'] >= 80) text-green-600
                            @elseif($satisfaction['quality_score'] >= 60) text-yellow-600
                            @else text-red-600 @endif mb-1">
                            {{ $satisfaction['quality_score'] }}%
                        </div>
                        <div class="text-xs text-gray-600">
                            {{ $satisfaction['avg_satisfaction'] }}/5 ⭐
                        </div>
                        <div class="text-xs text-gray-500 mt-1">
                            {{ $satisfaction['satisfaction_responses'] }}/{{ $satisfaction['total_tickets'] }} respuestas
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-1 mt-2">
                            <div class="
                                @if($satisfaction['quality_score'] >= 80) bg-green-600
                                @elseif($satisfaction['quality_score'] >= 60) bg-yellow-600
                                @else bg-red-600 @endif
                                h-1 rounded-full" style="width: {{ $satisfaction['quality_score'] }}%"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Resumen de Rendimiento -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Resumen de Productividad -->
            <div class="bg-white rounded-lg shadow-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Resumen de Productividad</h3>
                <div class="space-y-3">
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600">Alto Rendimiento (≥80%)</span>
                        <span class="font-bold text-green-600">{{ $efficiency['productivity_summary']['high_performers'] }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600">Rendimiento Promedio (60-79%)</span>
                        <span class="font-bold text-yellow-600">{{ $efficiency['productivity_summary']['average_performers'] }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600">Bajo Rendimiento (<60%)</span>
                        <span class="font-bold text-red-600">{{ $efficiency['productivity_summary']['low_performers'] }}</span>
                    </div>
                </div>
            </div>

            <!-- Resumen de Calidad -->
            <div class="bg-white rounded-lg shadow-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Resumen de Calidad</h3>
                <div class="space-y-3">
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600">Calidad Excelente (≥80)</span>
                        <span class="font-bold text-green-600">{{ $quality['quality_summary']['excellent_quality'] }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600">Buena Calidad (60-79)</span>
                        <span class="font-bold text-yellow-600">{{ $quality['quality_summary']['good_quality'] }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600">Necesita Mejora (<60)</span>
                        <span class="font-bold text-red-600">{{ $quality['quality_summary']['needs_improvement'] }}</span>
                    </div>
                </div>
            </div>

            <!-- KPIs Clave -->
            <div class="bg-white rounded-lg shadow-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">KPIs Clave</h3>
                <div class="space-y-3">
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600">Tasa de Reapertura</span>
                        <span class="font-bold text-red-600">{{ $quality['reopen_rate'] }}%</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600">Primera Resolución</span>
                        <span class="font-bold text-green-600">{{ $quality['first_call_resolution_rate'] }}%</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600">Tiempo Promedio Respuesta</span>
                        <span class="font-bold text-blue-600">{{ $efficiency['avg_first_response_time'] }}h</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-filament-panels::page>
